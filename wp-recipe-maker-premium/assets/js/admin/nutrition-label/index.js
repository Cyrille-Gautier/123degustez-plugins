import ReactDOM from 'react-dom';
import React from 'react';

import App from './App';

let layoutContainer = document.getElementById( 'wprmp-nutrition-label-layout' );

if (layoutContainer) {
	ReactDOM.render(
        <App/>,
		layoutContainer
	);
}