import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { content, noticeType, dismissDuration } = attributes; 
    const uniqueId = Math.random().toString(36).substr(2, 9); // simple unique ID   
    return (
        <div {...useBlockProps.save({
            className: `alertify-box alertify-${noticeType} alertify-dismissible`
        })}>
            <button className="alertify-close" aria-label="Close alert">
                &times;
            </button>
            <RichText.Content tagName="div" className="alertify-box-content" value={content} />
        </div>
    );
}