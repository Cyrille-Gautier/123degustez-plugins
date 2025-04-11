import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

import  { parseQuantity, formatQuantity } from '../shared/quantities';

window.WPRecipeMaker.advancedServings = {
    init() {
		// Replace input fields with functionality, unless on print page.
		if ( ! document.querySelector( 'body' ).classList.contains( 'wprm-print' ) ) {
			const inputElements = document.querySelectorAll( 'span.wprm-recipe-advanced-servings-input' );

			for ( let inputElement of inputElements ) {
				let link = document.createElement( 'a' );
				
				// Content.
				link.innerHTML = inputElement.innerHTML;

				// Link attributes.
				link.setAttribute( 'href', '#' );
				link.setAttribute( 'role', 'button' );

				// Get attributes of original element.
				const attrs = [...inputElement.attributes].reduce((attrs, attribute) => {
					attrs[attribute.name] = attribute.value;
					return attrs;
				}, {});

				// Set same attributes for new link.
				for ( let attr of Object.keys( attrs ) ) {
					link.setAttribute( attr, attrs[ attr ] );
				}
				
				// Replace.
				inputElement.parentNode.replaceChild( link, inputElement );

				// Attach functionality.
				link.onclick = (e) => {
					e.preventDefault();
					WPRecipeMaker.advancedServings.onClickInput( e.target );
				}

				// Show tooltip.
				if ( attrs.hasOwnProperty( 'aria-label' ) ) {
					tippy( link, {
						content: attrs['aria-label'],
					} );
				}
			}
		}

		// Changed servings directly.
		document.addEventListener( 'wprmAdjustedServings', (e) => {
			WPRecipeMaker.advancedServings.adjustedServingsDirectly( e.detail );
		} );
	},
	onClickInput( elem ) {
		if ( elem.classList.contains( 'wprm-recipe-advanced-servings-input-shape' ) ) {
			WPRecipeMaker.advancedServings.toggleShape( elem );
		} else if ( elem.classList.contains( 'wprm-recipe-advanced-servings-input-unit' ) ) {
			WPRecipeMaker.advancedServings.toggleUnit( elem );
		} else {
			WPRecipeMaker.advancedServings.changeNumber( elem );
		}
	},
	changeNumber( elem ) {
		const recipe = WPRecipeMaker.advancedServings.getRecipeFromElem( elem );

		if ( recipe ) {
			const label = elem.getAttribute( 'aria-label' );
			const value = elem.innerText;

			const newValue = prompt( `${label}:`, value );
			if ( newValue ) {
				const parsedValue = WPRecipeMaker.advancedServings.parse( newValue );

				if ( parsedValue && 0 < parsedValue ) {
					let changes = {};
					changes[ elem.dataset.type ] = parsedValue,
					WPRecipeMaker.advancedServings.updateRecipe( recipe.id, changes );
				}
			}
		}
	},
	toggleShape( elem ) {
		const recipe = WPRecipeMaker.advancedServings.getRecipeFromElem( elem );

		if ( recipe ) {
			const currentShape = recipe.current.shape;
			const newShape = 'round' === currentShape ? 'rectangle' : 'round';

			WPRecipeMaker.advancedServings.updateRecipe( recipe.id, { shape: newShape } );
		}
	},
	toggleUnit( elem ) {
		const recipe = WPRecipeMaker.advancedServings.getRecipeFromElem( elem );

		if ( recipe ) {
			const currentUnit = recipe.current.unit;
			const newUnit = 'cm' === currentUnit ? 'inch' : 'cm';

			WPRecipeMaker.advancedServings.updateRecipe( recipe.id, { unit: newUnit } );
		}
	},
	adjustedServingsDirectly( recipeId ) {
		const recipe = WPRecipeMaker.advancedServings.getRecipe( recipeId );
		const servingsRecipe = WPRecipeMaker.quantities.getRecipe( recipeId );

		if ( recipe && servingsRecipe ) {
			if ( recipe.current.servings !== servingsRecipe.servings ) {
				// Servings don't match anymore.
				if ( recipe.original.servings === servingsRecipe.servings ) {
					// Revert to original values.
					WPRecipeMaker.advancedServings.updateRecipe( recipe.id, { ...recipe.original } );
				} else {
					// Values unknown now.
					WPRecipeMaker.advancedServings.updateRecipe( recipe.id, {
						servings: servingsRecipe.servings,
						diameter: '?',
						width: '?',
						length: '?',
					} );
				}
			}
		}
	},
	getContainer( elem ) {
		let container = false;

		for ( var parent = elem.parentNode; parent && parent != document; parent = parent.parentNode ) {
			if ( parent.matches( '.wprm-recipe-advanced-servings-container' ) ) {
				container = parent;
				break;
			}
		}

		return container;
	},
	getRecipeFromElem( elem ) {
		const container = WPRecipeMaker.advancedServings.getContainer( elem );

		if ( container ) {
			return WPRecipeMaker.advancedServings.getRecipe( container.dataset.recipe );
		}

		return false;
	},
	getRecipe( recipeId ) {
		recipeId = parseInt( recipeId );

		if ( recipeId ) {
			if ( ! WPRecipeMaker.advancedServings.recipes.hasOwnProperty( `recipe-${recipeId}` ) ) {
				WPRecipeMaker.advancedServings.initRecipe( recipeId );
			}
			return this.recipes[ `recipe-${recipeId}` ];
		}

		return false;
	},
	setRecipe( recipeId, data ) {
		this.recipes[ `recipe-${recipeId}` ] = data;
	},
	initRecipe( recipeId ) {
		recipeId = parseInt( recipeId );

		if ( recipeId ) {
			const container = document.querySelector( '.wprm-recipe-advanced-servings-container' );

			if ( container ) {
				// Get original servings.
				const servingsRecipe = WPRecipeMaker.quantities.getRecipe( recipeId );

				let servings = 1;
				if ( servingsRecipe && servingsRecipe.originalServings && 0 < servingsRecipe.originalServings ) {
					servings = servingsRecipe.originalServings;
				}

				// Values passed along.
				const height = parseFloat( container.dataset.servingHeight );
				let diameter = parseFloat( container.dataset.servingDiameter );
				let width = parseFloat( container.dataset.servingWidth );
				let length = parseFloat( container.dataset.servingLength );

				const original = {
					servings,
					shape: container.dataset.servingShape,
					unit: container.dataset.servingUnit,
					height: isNaN( height ) ? 0 : height,
					diameter: isNaN( diameter ) ? 0 : diameter,
					width: isNaN( width ) ? 0 : width,
					length: isNaN( length ) ? 0 : length,
				}

				WPRecipeMaker.advancedServings.setRecipe( recipeId, {
					id: recipeId,
					container,
					original,
					current: { ...original },
				});
			}
		}
	},
	updateRecipe( recipeId, changes ) {
		const recipe = WPRecipeMaker.advancedServings.getRecipe( recipeId );

		if ( recipe ) {
			// Check if values need to change because of unit switch.
			if ( changes.hasOwnProperty( 'unit' ) && changes.unit !== recipe.current.unit ) {
				const inchToCm = 2.54;
				const factor = 'cm' === changes.unit ? inchToCm : 1 / inchToCm;

				if ( '?' !== recipe.current.diameter ) { changes.diameter = Math.round( recipe.current.diameter * factor ); }
				if ( '?' !== recipe.current.width ) { changes.width = Math.round( recipe.current.width * factor ); }
				if ( '?' !== recipe.current.length ) { changes.length = Math.round( recipe.current.length * factor ); }
				if ( '?' !== recipe.current.height ) { changes.height = Math.round( recipe.current.height * factor ); }
			}

			this.recipes[ `recipe-${recipeId}` ].current = {
				...recipe.current,
				...changes,
			};
			WPRecipeMaker.advancedServings.updateRecipeView( recipeId );
		}
	},
	updateRecipeView( recipeId ) {
		const recipe = WPRecipeMaker.advancedServings.getRecipe( recipeId );

		if ( recipe ) {
			const containers = document.querySelectorAll( `.wprm-recipe-advanced-servings-${ recipeId }-container` );

			for ( let container of containers ) {
				// Selected shape.
				const shapeInput = container.querySelector( '.wprm-recipe-advanced-servings-input-shape' );
				shapeInput.innerHTML = shapeInput.dataset[`shape${ recipe.current.shape[0].toUpperCase() + recipe.current.shape.slice(1) }`];

				if ( 'round' === recipe.current.shape ) {
					container.querySelector( '.wprm-recipe-advanced-servings-round' ).style.display = '';
					container.querySelector( '.wprm-recipe-advanced-servings-rectangle' ).style.display = 'none';
				} else {
					container.querySelector( '.wprm-recipe-advanced-servings-rectangle' ).style.display = '';
					container.querySelector( '.wprm-recipe-advanced-servings-round' ).style.display = 'none';
				}

				// Selected unit.
				const unitInputs = container.querySelectorAll( '.wprm-recipe-advanced-servings-input-unit' );

				for ( let unitInput of unitInputs ) {
					unitInput.innerHTML = unitInput.dataset[`unit${ recipe.current.unit[0].toUpperCase() + recipe.current.unit.slice(1) }`];
				}

				// Numbers.
				container.querySelector( '.wprm-recipe-advanced-servings-input-diameter' ).innerHTML = WPRecipeMaker.advancedServings.format( recipe.current.diameter );
				container.querySelector( '.wprm-recipe-advanced-servings-input-width' ).innerHTML = WPRecipeMaker.advancedServings.format( recipe.current.width );
				container.querySelector( '.wprm-recipe-advanced-servings-input-length' ).innerHTML = WPRecipeMaker.advancedServings.format( recipe.current.length );

				// Optional height.
				if ( recipe.current.height ) {
					container.querySelector( '.wprm-recipe-advanced-servings-input-height' ).innerHTML = WPRecipeMaker.advancedServings.format( recipe.current.height );
				}
			}

			// Updated serving values, update ingredient amounts as well.
			WPRecipeMaker.advancedServings.updateRecipeAmounts( recipeId );
		}
	},
	updateRecipeAmounts( recipeId ) {
		const recipe = WPRecipeMaker.advancedServings.getRecipe( recipeId );
		const factor = WPRecipeMaker.advancedServings.getServingsFactor( recipeId );

		if ( recipe && factor ) {
			const currentServings = recipe.original.servings * factor;
			WPRecipeMaker.advancedServings.recipes[ `recipe-${recipeId}` ].current.servings = currentServings;

			window.WPRecipeMaker.quantities.setServings( recipeId, currentServings );
		}
	},
	getServingsFactor( recipeId ) {
		const recipe = WPRecipeMaker.advancedServings.getRecipe( recipeId );

		if ( recipe ) {
			// Return false if some values are unknown.
			if ( '?' === recipe.current.height ) {
				return false;
			}
			if ( 'round' === recipe.current.shape ) {
				if ( '?' === recipe.current.diameter ) {
					return false;
				}
			} else {
				if ( '?' === recipe.current.width || '?' === recipe.current.length ) {
					return false;
				}
			}

			// All values we need are set, calculate.
			const usingHeight = 0 < recipe.original.height;

			let original = WPRecipeMaker.advancedServings.getArea( recipe.original );
			let current = WPRecipeMaker.advancedServings.getArea( recipe.current );

			if ( usingHeight ) {
				const originalHeight = 'inch' === recipe.original.unit ? recipe.original.height * 2.54 : recipe.original.height;
				const currentHeight = 'inch' === recipe.current.unit ? recipe.current.height * 2.54 : recipe.current.height;

				original *= originalHeight;
				current *= currentHeight;
			}

			return current / original;
		}

		return false;
	},
	getArea( values ) {
		let radius = values.diameter / 2;
		let width = values.width;
		let length = values.length;

		// Always use cm for area calculation.
		if ( 'inch' === values.unit ) {
			radius *= 2.54;
			width *= 2.54;
			length *= 2.54;
		}

		if ( 'round' === values.shape ) {
			return Math.PI * radius * radius;
		} else {
			return width * length;
		}
	},
	recipes: {},
	parse( quantity ) {
		return parseQuantity( quantity );
	},
	format( quantity ) {
		const formatted = formatQuantity( quantity, wprmp_public.settings.adjustable_servings_round_to_decimals );

		if ( isNaN( formatted ) ) {
			return quantity;
		} else {
			return formatted;
		}
	},
}

ready(() => {
	window.WPRecipeMaker.advancedServings.init();
});

function ready( fn ) {
    if (document.readyState != 'loading'){
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}