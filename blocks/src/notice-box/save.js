import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { content, noticeType } = attributes;

    return (
        <div {...useBlockProps.save({
            className: `alertify-box alertify-${noticeType}`
        })}>
            <RichText.Content tagName="div" className="alertify-box-content" value={content} />
        </div>
    );
}