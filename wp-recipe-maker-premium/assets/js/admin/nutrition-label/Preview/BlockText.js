import React from 'react';

const BlockText = (props) => {
    if ( ! props.block ) {
        return null;
    }

    let text = props.block.text;

    // Placeholders.
    text = text.replace( '%recipe_name%', 'My Demo Recipe' );

    return (
        <div className={ `wprmp-nutrition-label-block-text wprmp-nutrition-label-block-text-${ props.block.style }` }>
            { text }
        </div>
    );
}

export default BlockText;