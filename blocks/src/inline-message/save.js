import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { text, noticeType, showIcon, icon, shape } = attributes;

    return (
        <span {...useBlockProps.save({
            className: `alertify-inline alertify-${noticeType} ${shape === 'pill' ? 'alertify-pill' : 'alertify-square'}`
        })}>
            {showIcon && icon && <span className="alertify-inline-icon">{icon}</span>}
            <RichText.Content tagName="span" className="alertify-inline-text" value={text} />
        </span>
    );
}