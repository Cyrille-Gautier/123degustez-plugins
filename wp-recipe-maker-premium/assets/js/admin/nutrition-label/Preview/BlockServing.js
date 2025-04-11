import React from 'react';

const BlockServing = (props) => {
    if ( ! props.block ) {
        return null;
    }

    return (
        <div className={ `wprmp-nutrition-label-block-serving wprmp-nutrition-label-block-serving-${ props.block.style }`}>
            <div className="wprmp-nutrition-label-block-serving-text">{ props.block.text }</div>
            <div className="wprmp-nutrition-label-block-serving-spacer">&nbsp;</div>
            <div className="wprmp-nutrition-label-block-serving-value">126 { wprm_admin.settings.nutrition_default_serving_unit }</div>
        </div>
    );
}

export default BlockServing;