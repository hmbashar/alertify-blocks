import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    RichText,
    InspectorControls
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, RangeControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

export default function Edit({ attributes, setAttributes, clientId }) {
    const { content, noticeType, dismissDuration, alertId } = attributes;

    // Generate unique alertId only once
    useEffect(() => {
        if (!alertId) {
            const newId = clientId || Math.random().toString(36).substr(2, 9);
            setAttributes({ alertId: newId });
        }
    }, []);

    const blockProps = useBlockProps({
        className: `alertify-box alertify-${noticeType} alertify-dismissible`,
        'data-alert-id': alertId // use the stored alertId
    });

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Alert Settings', 'alertify-blocks')}>
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
                       <RangeControl
                        label={__('Dismiss Duration (hours)', 'alertify-blocks')}
                        value={dismissDuration}
                        min={1}
                        max={168} // up to 7 days
                        onChange={(value) => setAttributes({ dismissDuration: value })}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <button className="alertify-close" aria-label={__('Close alert', 'alertify-blocks')}>
                    &times;
                </button>
                <RichText
                    tagName="div"
                    className="alertify-box-content"
                    value={content}
                    allowedFormats={['core/bold', 'core/italic', 'core/link']}
                    onChange={(value) => setAttributes({ content: value })}
                    placeholder={__('Add your alert message…', 'alertify-blocks')}
                />
            </div>
        </>
    );
}