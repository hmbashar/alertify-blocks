import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    RichText,
    InspectorControls
} from '@wordpress/block-editor';
import {
    PanelBody,
    SelectControl,
    RangeControl,
    ToggleControl,
    TextControl
} from '@wordpress/components';

import { useEffect } from '@wordpress/element';

export default function Edit({ attributes, setAttributes, clientId }) {
    const { text, noticeType, showIcon, icon, shape, alertId } = attributes;

    // Set unique ID once
    useEffect(() => {
        if (!alertId) {
            const newId = clientId || Math.random().toString(36).substr(2, 9);
            setAttributes({ alertId: newId });
        }
    }, []);

    const blockProps = useBlockProps({
        className: `alertify-inline alertify-${noticeType} ${shape === 'pill' ? 'alertify-pill' : 'alertify-square'}`
    });

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Inline Message Settings', 'alertify-blocks')}>
                    <SelectControl
                        label={__('Notice Type', 'alertify-blocks')}
                        value={noticeType}
                        options={[
                            { label: __('Info', 'alertify-blocks'), value: 'info' },
                            { label: __('Success', 'alertify-blocks'), value: 'success' },
                            { label: __('Warning', 'alertify-blocks'), value: 'warning' },
                            { label: __('Error', 'alertify-blocks'), value: 'error' },
                        ]}
                        onChange={(value) => setAttributes({ noticeType: value })}
                    />
                    <SelectControl
                        label={__('Badge Shape', 'alertify-blocks')}
                        value={shape}
                        options={[
                            { label: __('Pill (rounded)', 'alertify-blocks'), value: 'pill' },
                            { label: __('Square', 'alertify-blocks'), value: 'square' },
                        ]}
                        onChange={(value) => setAttributes({ shape: value })}
                    />
                    <ToggleControl
                        label={__('Show Icon?', 'alertify-blocks')}
                        checked={showIcon}
                        onChange={(value) => setAttributes({ showIcon: value })}
                    />
                    {showIcon && (
                        <TextControl
                            label={__('Icon (emoji or text)', 'alertify-blocks')}
                            value={icon}
                            onChange={(value) => setAttributes({ icon: value })}
                            placeholder="e.g., ⚠️ or ✔️"
                        />
                    )}
                </PanelBody>
            </InspectorControls>

            <span {...blockProps}>
                {showIcon && icon && <span className="alertify-inline-icon">{icon}</span>}
                <RichText
                    tagName="span"
                    className="alertify-inline-text"
                    value={text}
                    allowedFormats={['core/bold', 'core/italic']}
                    onChange={(value) => setAttributes({ text: value })}
                    placeholder={__('Add inline message…', 'alertify-blocks')}
                />
            </span>
        </>
    );
}