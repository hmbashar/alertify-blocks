import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { content, noticeType, dismissDuration, alertId } = attributes;  
    return (
        <div {...useBlockProps.save({
            className: `alertify-box alertify-${noticeType} alertify-dismissible`,
            'data-alert-id': alertId,
            'data-dismiss-duration': dismissDuration
        })}>
            <button className="alertify-close" aria-label="Close alert">
                &times;
            </button>
            <RichText.Content tagName="div" className="alertify-box-content" value={content} />
        </div>
    );
}