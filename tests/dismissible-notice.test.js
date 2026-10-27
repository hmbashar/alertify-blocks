'use strict';

const { test, describe, beforeEach } = require('node:test');
const assert = require('node:assert/strict');

// ---------- Minimal DOM stubs ----------

class FakeElement {
    constructor(classes = [], datasetValues = {}) {
        this._classes   = new Set(classes);
        this.dataset    = datasetValues;
        this._children  = {};
        this._removed   = false;
        this._listeners = {};

        const self = this;
        this.classList = {
            contains(cls) { return self._classes.has(cls); },
            add(cls)      { self._classes.add(cls); },
            remove(cls)   { self._classes.delete(cls); },
        };
    }

    querySelector(selector) { return this._children[selector] ?? null; }

    addEventListener(event, fn) {
        if (!this._listeners[event]) this._listeners[event] = [];
        this._listeners[event].push(fn);
    }

    _trigger(event) {
        (this._listeners[event] || []).forEach(fn => fn());
    }

    remove() { this._removed = true; }
}

// ---------- Minimal localStorage stub ----------

class FakeLocalStorage {
    constructor() { this._store = {}; }
    getItem(key)        { return Object.prototype.hasOwnProperty.call(this._store, key) ? this._store[key] : null; }
    setItem(key, value) { this._store[key] = String(value); }
    removeItem(key)     { delete this._store[key]; }
    clear()             { this._store = {}; }
}

// ---------- Core logic extracted from dismissible-notice.js ----------

function processDismissibleBlock(alertBox, storage) {
    const alertId         = alertBox.dataset.alertId;
    const dismissDuration = parseInt(alertBox.dataset.dismissDuration, 10) || 24;
    const dismissKey      = `alertify_${alertId}`;

    const dismissed = storage.getItem(dismissKey);
    if (dismissed) {
        const dismissedTime = parseInt(dismissed, 10);
        const now           = Date.now();
        const hoursPassed   = (now - dismissedTime) / (1000 * 60 * 60);
        if (hoursPassed < dismissDuration) {
            alertBox.remove();
            return;
        }
    }

    const closeBtn = alertBox.querySelector('.alertify-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            alertBox.remove();
            storage.setItem(dismissKey, Date.now().toString());
        });
    }
}

// ---------- Tests ----------

describe('dismissible-notice logic', () => {
    let storage;

    beforeEach(() => {
        storage = new FakeLocalStorage();
    });

    test('block is NOT removed when no dismiss record exists', () => {
        const closeBtn = new FakeElement();
        const alertBox = new FakeElement([], { alertId: 'abc123', dismissDuration: '24' });
        alertBox._children['.alertify-close'] = closeBtn;

        processDismissibleBlock(alertBox, storage);

        assert.equal(alertBox._removed, false, 'block should still be visible');
    });

    test('block IS removed when dismiss record is recent (within duration)', () => {
        const alertBox = new FakeElement([], { alertId: 'abc123', dismissDuration: '24' });
        storage.setItem('alertify_abc123', String(Date.now() - 1 * 60 * 60 * 1000));

        processDismissibleBlock(alertBox, storage);

        assert.equal(alertBox._removed, true, 'block should be removed (within duration)');
    });

    test('block is NOT removed when dismiss record has expired', () => {
        const closeBtn = new FakeElement();
        const alertBox = new FakeElement([], { alertId: 'abc123', dismissDuration: '24' });
        alertBox._children['.alertify-close'] = closeBtn;
        storage.setItem('alertify_abc123', String(Date.now() - 25 * 60 * 60 * 1000));

        processDismissibleBlock(alertBox, storage);

        assert.equal(alertBox._removed, false, 'expired dismiss should not hide block');
    });

    test('clicking close removes block and saves timestamp', () => {
        const closeBtn = new FakeElement();
        const alertBox = new FakeElement([], { alertId: 'xyz789', dismissDuration: '48' });
        alertBox._children['.alertify-close'] = closeBtn;

        processDismissibleBlock(alertBox, storage);
        closeBtn._trigger('click');

        assert.equal(alertBox._removed, true, 'block should be removed after close click');
        const saved = storage.getItem('alertify_xyz789');
        assert.ok(saved, 'dismiss timestamp should be saved');
        assert.ok(Number(saved) <= Date.now(), 'saved timestamp should be <= now');
    });

    test('default duration is 24h when data-dismiss-duration is invalid', () => {
        const alertBox = new FakeElement([], { alertId: 'def456', dismissDuration: 'bad' });
        storage.setItem('alertify_def456', String(Date.now() - 23 * 60 * 60 * 1000));

        processDismissibleBlock(alertBox, storage);

        assert.equal(alertBox._removed, true, 'default 24h should apply');
    });

    test('dismiss keys are namespaced — no cross-block collision', () => {
        const alertBox1 = new FakeElement([], { alertId: 'block1', dismissDuration: '24' });
        const alertBox2 = new FakeElement([], { alertId: 'block2', dismissDuration: '24' });
        storage.setItem('alertify_block1', String(Date.now() - 1 * 60 * 60 * 1000));

        processDismissibleBlock(alertBox1, storage);
        processDismissibleBlock(alertBox2, storage);

        assert.equal(alertBox1._removed, true,  'block1 should be hidden');
        assert.equal(alertBox2._removed, false,  'block2 should not be affected');
    });

    test('missing close button does not crash', () => {
        const alertBox = new FakeElement([], { alertId: 'noclose', dismissDuration: '24' });

        assert.doesNotThrow(() => {
            processDismissibleBlock(alertBox, storage);
        });

        assert.equal(alertBox._removed, false, 'block should remain visible');
    });

    test('dismiss at boundary (exactly at duration) does NOT hide block', () => {
        const closeBtn = new FakeElement();
        const alertBox = new FakeElement([], { alertId: 'boundary', dismissDuration: '24' });
        alertBox._children['.alertify-close'] = closeBtn;
        // Exactly 24 hours ago — hoursPassed === dismissDuration, NOT < duration
        storage.setItem('alertify_boundary', String(Date.now() - 24 * 60 * 60 * 1000));

        processDismissibleBlock(alertBox, storage);

        assert.equal(alertBox._removed, false, 'at boundary exactly, block should be visible again');
    });
});
