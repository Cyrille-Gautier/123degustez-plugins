import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

import  { parseQuantity, formatQuantity } from '../shared/quantities';

window.WPRecipeMaker.quantities = {
    init() {
		// Optional initial servings to set after init.
		let initialServingsToSet = false;
		let initialServingsToSetFor = [];

		// Replace serving elements with functionality.
		const servingElements = document.querySelectorAll( '.wprm-recipe-servings' );

		for ( let servingElement of servingElements ) {
			if ( ! servingElement.dataset.hasOwnProperty( 'servings' ) ) {
				// Check for initial servings to set after init.
				const initialServings = servingElement.dataset.hasOwnProperty( 'initialServings' ) ? parseFloat( servingElement.dataset.initialServings ) : 0;
				const recipeId = servingElement.dataset.recipe;

				if ( ! isNaN( initialServings ) && 0 < initialServings ) {
					initialServingsToSet = initialServings;
				}
				if ( recipeId ) {
					initialServingsToSetFor.push( recipeId );
				}

				// Init different adjustable servings.
				const servings = this.parse( servingElement.innerText );

				if ( 0 < servings ) {
					servingElement.dataset.servings = servings;
					servingElement.dataset.originalServings = servings;

					// No adjusting on print pages.
					if ( ! document.querySelector( 'body' ).classList.contains( 'wprm-print' ) ) {
						if ( 'modern' === wprmp_public.settings.recipe_template_mode ) {
							if ( servingElement.classList.contains( 'wprm-recipe-servings-adjustable-tooltip' ) ) {
								this.initTooltipSlider( servingElement );
							} else if ( servingElement.classList.contains( 'wprm-recipe-servings-adjustable-text' ) ) {
								this.initTextInput( servingElement );
							} else if ( servingElement.classList.contains( 'wprm-recipe-servings-adjustable-text-buttons' ) ) {
								this.initTextButtonsInput( servingElement );
							}
						} else if ( wprmp_public.settings.features_adjustable_servings ) {
							if ( 'text_field' === wprmp_public.settings.servings_changer_display ) {
								this.initTextInput( servingElement );
							} else { // Default = Tooltip Slider
								this.initTooltipSlider( servingElement );
							}
						}
					}
				}
			}
		}
		// Add event listeners.
		document.addEventListener( 'input', function(e) {
			if ( e.target.matches( 'input.wprm-recipe-servings' ) ) {
				WPRecipeMaker.quantities.inputChange( e.target );
			}
		}, false );
		document.addEventListener( 'change', function(e) {
			if ( e.target.matches( 'input.wprm-recipe-servings' ) ) {
				WPRecipeMaker.quantities.inputChange( e.target );
			}
		}, false );
		document.addEventListener( 'click', function(e) {
			if ( e.target.matches( '.wprm-recipe-servings-change' ) ) {
				WPRecipeMaker.quantities.changeClick( e.target );
			}
			if ( e.target.matches( '.wprm-recipe-adjustable-servings' ) ) {
				WPRecipeMaker.quantities.multiplierClick( e.target );
			}
        }, false );

		// Set initial servings after init.
		initialServingsToSetFor = initialServingsToSetFor.map( ( id ) => parseInt( id ) );
		initialServingsToSetFor = [ ...new Set( initialServingsToSetFor ) ];

		if ( initialServingsToSet ) {
			for ( let recipeId of initialServingsToSetFor ) {
				this.setServings( recipeId, initialServingsToSet );
			}
		}
	},
	initTextInput( elem ) {
		let servings = elem.dataset.servings,
			recipeId = elem.dataset.recipe,
			ariaLabel = elem.getAttribute( 'aria-label' );

		// Backwards compatibility.
		if ( ! recipeId ) {
			for ( var parent = elem.parentNode; parent && parent != document; parent = parent.parentNode ) {
				if ( parent.matches( '.wprm-recipe-container' ) ) {
					recipeId = parent.dataset.recipeId;
					break;
				}
			}
		}

		if ( recipeId ) {
			// Construct input field.
			const input = '<input type="number" class="wprm-recipe-servings wprm-recipe-servings-' + recipeId + '" min="0" step="any" value="' + servings + '" data-recipe="' + recipeId + '" aria-label="' + ariaLabel + '" />';
			elem.outerHTML = input;
		}
	},
	initTextButtonsInput( elem ) {
		let servings = elem.dataset.servings,
			recipeId = elem.dataset.recipe,
			ariaLabel = elem.getAttribute( 'aria-label' );

		// Backwards compatibility.
		if ( ! recipeId ) {
			for ( var parent = elem.parentNode; parent && parent != document; parent = parent.parentNode ) {
				if ( parent.matches( '.wprm-recipe-container' ) ) {
					recipeId = parent.dataset.recipeId;
					break;
				}
			}
		}

		if ( recipeId ) {
			// Button style.
			let buttonStyle = '';
			buttonStyle += 'background-color: ' + elem.dataset.buttonBackground + ';';
			buttonStyle += 'border-color: ' + elem.dataset.buttonBackground + ';';
			buttonStyle += 'color: ' + elem.dataset.buttonAccent + ';';
			buttonStyle += 'border-radius: ' + elem.dataset.buttonRadius + ';';

			// Input style.
			let inputStyle = '';
			inputStyle += 'border-color: ' + elem.dataset.buttonBackground + ';';

			// Construct input field.
			const decrement = '<span class="wprm-recipe-servings-decrement wprm-recipe-servings-change" style="' + buttonStyle + '">–</span>';
			const input = '<input type="text" class="wprm-recipe-servings wprm-recipe-servings-' + recipeId + '" min="0" step="any" value="' + servings + '" data-recipe="' + recipeId + '" aria-label="' + ariaLabel + '" style="' + inputStyle + '"/>';
			const increment = '<span class="wprm-recipe-servings-increment wprm-recipe-servings-change" style="' + buttonStyle + '">+</span>';
			elem.outerHTML = '<span class="wprm-recipe-servings-text-buttons-container">' + decrement + input + increment + '</span>';
		}
	},
	initTooltipSlider( elem ) {
		let recipeId = elem.dataset.recipe,
			ariaLabel = elem.getAttribute( 'aria-label' );

		// Backwards compatibility.
		if ( ! recipeId ) {
			for ( var parent = elem.parentNode; parent && parent != document; parent = parent.parentNode ) {
				if ( parent.matches( '.wprm-recipe-container' ) ) {
					recipeId = parent.dataset.recipeId;
					break;
				}
			}
		}

		if ( recipeId ) {
			// Wrap with link.
			let link = document.createElement('a');
			link.href = '#';
			link.classList.add( 'wprm-recipe-servings-link' );
			link.setAttribute( 'aria-label', ariaLabel );
			link.onclick = ( e ) => {
				e.preventDefault();
			};

			elem.parentNode.insertBefore( link, elem );
			link.appendChild( elem );

			// Add tooltip.
			tippy( link, {
				content: '',
				onShow(instance) {
					const recipe = WPRecipeMaker.quantities.getRecipe( recipeId );

					if ( recipe ) {
						const servings = recipe.servings; 
						const max = 20 < 2 * servings ? 2 * servings : 20;

						const countDecimals = function (value) {
							if( Math.floor( value ) === value ) return 0;
							return value.toString().split(".")[1].length || 0;
						}

						const decimals = countDecimals( servings );
						const step = 1 / ( Math.pow( 10, decimals ) );
	
						instance.setContent( `<input type="range" min="1" max="${ max }" step="${ step }" value="${ servings }" data-recipe="${ recipeId }" class="wprm-recipe-servings-slider wprm-recipe-servings-${ recipeId }" aria-label="${ ariaLabel }" oninput="WPRecipeMaker.quantities.inputChange(this)" onchange="WPRecipeMaker.quantities.inputChange(this)"></input>` );
					} else {
						return false;
					}
				},
				allowHTML: true,
				interactive: true,
				delay: [0, 250],
			});
		}
	},
	inputChange( input ) {
		let servings = input.value,
			recipeId = input.dataset.recipe;

		if ( servings ) {
			// Track action for analytics.
			const type = input.classList.contains( 'wprm-recipe-servings-slider' ) ? 'slider' : 'input';
			window.WPRecipeMaker.analytics.registerActionOnce( recipeId, wprm_public.post_id, 'adjust-servings', {
				type,
			});

			this.setServings( recipeId, servings );
		}
	},
	changeClick( elem ) {
		const parent = elem.closest( '.wprm-recipe-servings-text-buttons-container' );

		if ( parent ) {
			const input = parent.querySelector( 'input' );
			const servings = this.parse( input.value );
			let newServings = servings;

			// Don't go to 0 or below when decrementing.
			if ( elem.classList.contains( 'wprm-recipe-servings-decrement' ) && servings > 1 ) {
				newServings--;
			} else if ( elem.classList.contains( 'wprm-recipe-servings-increment' ) ) {
				newServings++;
			}

			if ( newServings !== servings ) {
				input.value = newServings;

				// Trigger change.
				this.inputChange( input );
			}
		}
	},
	multiplierClick( elem ) {
		if ( ! elem.classList.contains( 'wprm-toggle-active' ) || '?' === elem.dataset.multiplier ) {
			const multiplier = elem.dataset.multiplier,
				recipeId = elem.dataset.recipe,
				servings = elem.dataset.servings;
			
			let newServings = false;

			if ( '?' === multiplier ) {
				newServings = prompt( elem.getAttribute( 'aria-label' ) );

				if ( newServings ) {
					newServings = this.parse( newServings );
				}
			} else {
				newServings = this.parse( servings ) * this.parse( multiplier );
			}

			if ( newServings ) {
				// Track action for analytics.
				window.WPRecipeMaker.analytics.registerActionOnce( recipeId, wprm_public.post_id, 'adjust-servings', {
					type: 'button',
				});

				this.setServings( recipeId, newServings );
			}
		}
	},
	setServings( recipeId, servings ) {
		const recipe = this.getRecipe( recipeId );

		if ( recipe ) {
			recipe.servingsRaw = servings;
			recipe.servings = this.parse( servings );
			this.updateRecipe( recipe );
		}
	},
	initRecipe( recipeId ) {
		recipeId = parseInt( recipeId );

		if ( recipeId ) {
			// Original values for recipe.
			let originalServings = 1;
			let originalServingsKnown = false;

			// Create adjustable elements.
			let adjustables = [];

			const containers = document.querySelectorAll( `#wprm-recipe-container-${ recipeId }, .wprm-recipe-roundup-item-${ recipeId }, .wprm-print-recipe-${ recipeId }, .wprm-recipe-${ recipeId }-ingredients-container, .wprm-recipe-${ recipeId }-instructions-container` );
			for ( let container of containers ) {
				// Check for original servings.
				if ( container.dataset.hasOwnProperty( 'servings' ) ) {
					const containerServings = this.parse( container.dataset.servings );

					if ( 0 < containerServings ) {
						originalServings = this.parse( container.dataset.servings );
						originalServingsKnown = true;
					}
				}
			}

			// Loop over containers again, now that we now the servings.
			for ( let container of containers ) {
				// Make ingredient amounts and adjustable shortcode adjustable.
				const quantityElems = container.querySelectorAll( '.wprm-recipe-ingredient-amount, .wprm-dynamic-quantity' );
				for ( let quantityElem of quantityElems ) {
					// Only do this once.
					if ( 0 === quantityElem.querySelectorAll( '.wprm-adjustable' ).length ) {
						// Surround all the number blocks
						let quantity = quantityElem.innerText;
		
						// Special case: .5
						if ( /^\.\d+\s*$/.test( quantity ) ) {
							quantityElem.innerHTML = '<span class="wprm-adjustable">' + quantity + '</span>';
						} else {
							const fractions = '\u00BC\u00BD\u00BE\u2150\u2151\u2152\u2153\u2154\u2155\u2156\u2157\u2158\u2159\u215A\u215B\u215C\u215D\u215E';
							const number_regex = '[\\d'+fractions+']([\\d'+fractions+'.,\\/\\s]*[\\d'+fractions+'])?';
							const substitution = '<span class="wprm-adjustable">$&</span>';
		
							quantity = quantity.replace( new RegExp( number_regex, 'g' ), substitution );
							quantityElem.innerHTML = quantity;
						}
					}
				}

				// WP Ultimate Recipe compatibility.
				const wpurpElems = container.querySelectorAll( '.wpurp-adjustable-quantity' );
				for ( let wpurpElem of wpurpElems ) {
					wpurpElem.classList.add( 'wprm-adjustable' );
				}

				// Init all adjustables.
				const adjustableElems = container.querySelectorAll( '.wprm-adjustable' );
				for ( let adjustableElem of adjustableElems ) {
					// Don't add again if already part of adjustables.
					if ( -1 !== adjustables.findIndex( (existingAdjustable) => existingAdjustable.elem === adjustableElem ) ) {
						continue;
					}

					// Check for linked ingredient name.
					const parentIngredient = adjustableElem.closest( '.wprm-recipe-ingredient' );
					let linkedIngredientName = false;
					let linkedIngredientNameOriginal = false;
					let ingredientUid = false;

					if ( parentIngredient ) {
						const nameElem = parentIngredient.querySelector( '.wprm-recipe-ingredient-name' );
						linkedIngredientName = nameElem ? nameElem : false;

						if ( linkedIngredientName ) {
							linkedIngredientNameOriginal = linkedIngredientName.innerHTML;
						}

						if ( parentIngredient.dataset.hasOwnProperty( 'uid' ) ) {
							ingredientUid = parseInt( parentIngredient.dataset.uid );
						}
					}

					// Add to adjustables.
					adjustables.push( {
						elem: adjustableElem,
						original: adjustableElem.innerText,
						unitQuantity: this.parse( adjustableElem.innerText ) / originalServings,
						parentIngredient,
						linkedIngredientName: linkedIngredientName,
						linkedIngredientNameOriginal: linkedIngredientNameOriginal,
						ingredientUid,
					} );
				}
			}

			this.recipes[ `recipe-${recipeId}` ] = {
				id: recipeId,
				servings: originalServings,
				servingsRaw: originalServings,
				originalServings,
				originalServingsKnown,
				adjustables,
				system: 1,
				originalSystem: 1,
			}
		}
	},
	getRecipe( recipeId ) {
		recipeId = parseInt( recipeId );

		if ( recipeId ) {
			if ( ! this.recipes.hasOwnProperty( `recipe-${recipeId}` ) ) {
				this.initRecipe( recipeId );
			}
			return this.recipes[ `recipe-${recipeId}` ];
		}

		return false;
	},
	updateIngredients( recipe ) {
		for ( let adjustable of recipe.adjustables ) {
			// Make name singular/plural as needed.
			if ( adjustable.linkedIngredientName ) {
				const singular = adjustable.linkedIngredientName.dataset.hasOwnProperty( 'nameSingular' ) ? adjustable.linkedIngredientName.dataset.nameSingular : false;
				const plural = adjustable.linkedIngredientName.dataset.hasOwnProperty( 'namePlural' ) ? adjustable.linkedIngredientName.dataset.namePlural : false;

				if ( recipe.servings === recipe.originalServings ) {
					// Only do this if the ingredient actually had singular and plural defined. Prevents any adjustable shortcodes inside from breaking.
					if ( singular || plural ) {
						adjustable.linkedIngredientName.innerHTML = adjustable.linkedIngredientNameOriginal;
					}
				} else {
					const newQuantity = recipe.servings * adjustable.unitQuantity;

					if ( ! isNaN( newQuantity ) && singular && plural ) {
						let newName = plural;
						if ( newQuantity <= 1 ) {
							newName = singular;
						}

						// Check for link inside.
						const link = adjustable.linkedIngredientName.querySelector( 'a' );

						if ( link ) {
							link.innerText = newName;
						} else {
							adjustable.linkedIngredientName.innerText = newName;
						}
					}
				}
			}

			// Update any associated ingredients based on UID.
			if ( adjustable.parentIngredient && false !== adjustable.ingredientUid ) {
				const linkedIngredients = document.querySelectorAll( '.wprm-recipe-instruction-ingredient-' + recipe.id + '-' + adjustable.ingredientUid );

				if ( 0 < linkedIngredients.length ) {
					let ingredientParts = [];

					const amount = adjustable.parentIngredient.querySelector( '.wprm-recipe-ingredient-amount' );
					if ( amount ) { ingredientParts.push( amount.innerText.trim() ); }

					const unit = adjustable.parentIngredient.querySelector( '.wprm-recipe-ingredient-unit' );
					if ( unit ) { ingredientParts.push( unit.innerText.trim() ); }

					const name = adjustable.parentIngredient.querySelector( '.wprm-recipe-ingredient-name' );
					if ( name ) { ingredientParts.push( name.innerText.trim() ); }

					const ingredientString = ingredientParts.join( ' ' );

					if ( ingredientString ) {
						for ( let linkedIngredient of linkedIngredients ) {
							let separator = '';

							if ( linkedIngredient.dataset.hasOwnProperty( 'separator' ) ) {
								separator = linkedIngredient.dataset.separator;
							}

							linkedIngredient.innerText = ingredientString + separator;
						}
					}
				}
			}
		}
	},
	updateRecipe( recipe ) {
		// Update adjustables.
		for ( let adjustable of recipe.adjustables ) {
			if ( recipe.servings === recipe.originalServings ) {
				adjustable.elem.textContent = adjustable.original;
			} else {
				const newQuantity = recipe.servings * adjustable.unitQuantity;

				if ( ! isNaN( newQuantity ) ) {
					adjustable.elem.textContent = this.format( newQuantity );
				}
			}
		}

		// Update linked ingredients.
		this.updateIngredients( recipe );

		// Update servings.
		const servingElems = document.querySelectorAll( '.wprm-recipe-servings-' + recipe.id );

		for ( let servingElem of servingElems ) {
			const roundedServings = formatQuantity( recipe.servings, 1, false );
			servingElem.textContent = roundedServings;
			servingElem.dataset.servings = recipe.servings;

			// Use raw value (4. instead of 4) for input fields.
			if ( 'input' === servingElem.tagName.toLowerCase() ) {
				if ( typeof recipe.servingsRaw === 'string' || recipe.servingsRaw instanceof String ) {
					// Servings set through text input field, set as is.
					servingElem.value = recipe.servingsRaw;
				} else {
					// Servings set through other field (buttons, slider, advanced servings), set rounded value.
					servingElem.value = roundedServings;
				}
			} else {
				servingElem.value = recipe.servings;
			}
		}

		// Check multiplier buttons.
		const multiplierContainers = document.querySelectorAll( '.wprm-recipe-adjustable-servings-' + recipe.id + '-container' );
		const multiplier = this.parse( recipe.servings / recipe.originalServings );

		for ( let multiplierContainer of multiplierContainers ) {
			const multiplierButtons = multiplierContainer.querySelectorAll( '.wprm-recipe-adjustable-servings' );
			let matchFound = false;

			for ( let multiplierButton of multiplierButtons ) {
				multiplierButton.classList.remove( 'wprm-toggle-active' );

				if ( this.parse( multiplierButton.dataset.multiplier ) === multiplier ) {
					matchFound = true;
					multiplierButton.classList.add( 'wprm-toggle-active' );
				} else if ( '?' === multiplierButton.dataset.multiplier && ! matchFound ) {
					multiplierButton.classList.add( 'wprm-toggle-active' );
				}
			}
		}

		document.dispatchEvent( new CustomEvent( 'wprmAdjustedServings', { detail: recipe.id } ) );
	},
	recipes: {},
	parse( quantity ) {
		return parseQuantity( quantity );
	},
	format( quantity ) {
		return formatQuantity( quantity, wprmp_public.settings.adjustable_servings_round_to_decimals, true );
	},
}

ready(() => {
	window.WPRecipeMaker.quantities.init();
});

function ready( fn ) {
    if (document.readyState != 'loading'){
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}