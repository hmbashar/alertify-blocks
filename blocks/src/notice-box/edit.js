import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    RichText,
    InspectorControls,
    BlockControls,
    AlignmentToolbar
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { content, noticeType } = attributes;

    const blockProps = useBlockProps({
        className: `alertify-box alertify-${noticeType}`
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
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
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