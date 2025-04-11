window.WPRMPremiumPrint = {
	init() {
        // Hide empty private notes.
        window.WPRecipeMaker.privateNotes.hideEmpty();

        // Handle adjustable servings and unit conversion.
        this.servingsInput = document.querySelector( '#wprm-print-servings' );
        this.initServingsInput();

        this.unitConversionChanger = document.querySelector( '#wprm-print-unit-conversion-container' );
        this.initUnitConversionChanger();
        
        // Check if initial servings passed along.
        if ( window.hasOwnProperty( 'wprmp_print_recipes' ) ) {
            this.setInitialServings( window.wprmp_print_recipes );
        }

        // On args change.
        document.addEventListener( 'wprmPrintArgs', () => {
            this.onArgsChange();
        });
    },
    onArgsChange(  ) {
        const args = window.WPRMPrint.args;

        if ( args.hasOwnProperty( 'servings' ) ) {
            this.setServings( args.servings );
        }
        if ( args.hasOwnProperty( 'system' ) ) {
            this.setSystem( args.system );
        }
        if ( args.hasOwnProperty( 'advancedServings' ) ) {
            this.setAdvancedServings( args.advancedServings );
        }
    },
    servingsInput: false,
    initServingsInput() {
        if ( this.servingsInput ) {
            // On input change.
            this.servingsInput.addEventListener( 'change', () => {
                this.setServings( this.servingsInput.value );
            });

            // On click servings change.
            const servingsChangers = [ ...document.querySelectorAll( '.wprm-print-servings-change' )];

            for ( let servingsChanger of servingsChangers ) {
                // Event listener.
                servingsChanger.addEventListener( 'click', () => {
                    this.onClickServingsChange( servingsChanger );
                });
            }

            // Find servings unit in recipe.
            const recipeServingsUnitElem = document.querySelector( '.wprm-recipe-servings-unit' );

            if ( recipeServingsUnitElem ) {
                const recipeServingsUnit = recipeServingsUnitElem.innerText.trim();
                
                if ( recipeServingsUnit ) {
                    document.querySelector( '#wprm-print-servings-unit' ).innerText = recipeServingsUnit;
                }
            }
        }
    },
    onClickServingsChange( button ) {
        if ( this.servingsInput ) {
            let servingsValue = parseFloat( this.servingsInput.value );

            if ( button.classList.contains( 'wprm-print-servings-increment' ) ) {
                servingsValue++;
            } else {
                servingsValue--;
            }
            this.setServings( servingsValue );
        }
    },
    setServings( servings ) {
        // Make sure it's valid.
        servings = parseFloat( servings );
        servings = isNaN( servings ) || servings <= 0 ? false : servings;

        if ( false !== servings && window.WPRecipeMaker.hasOwnProperty( 'quantities' )) {
            if ( this.servingsInput ) {
                this.servingsInput.value = servings;
            }

            const recipes = document.querySelectorAll( '.wprm-print-recipe' );

            for ( let recipe of recipes ) {
                const recipeId = recipe.dataset.recipeId;
                if ( recipeId ) {
                    WPRecipeMaker.quantities.setServings( recipeId, servings );
                }
            }
        }
    },
    setAdvancedServings( servings ) {
        if ( false !== servings && window.WPRecipeMaker.hasOwnProperty( 'advancedServings' )) {
            WPRecipeMaker.advancedServings.setRecipe( servings.id, servings );
            WPRecipeMaker.advancedServings.updateRecipeView( servings.id );
        }
    },
    unitConversionChanger: false,
    initUnitConversionChanger() {
        if ( this.unitConversionChanger ) {
            const unitSystems = this.unitConversionChanger.querySelectorAll( '.wprm-unit-conversion' );

            // On click.
            for ( let unitSystem of unitSystems ) {
                unitSystem.addEventListener( 'click', () => {
                    this.setSystem( unitSystem.dataset.system );
                });
            }
        }
    },
    setSystem( system ) {
        // Make sure it's valid.
        system = parseInt( system );
        system = isNaN( system ) || system < 0 ? false : system;

        if ( false !== system && window.WPRecipeMaker.hasOwnProperty( 'conversion' ) ) {
            const recipes = document.querySelectorAll( '.wprm-print-recipe' );

            for ( let recipe of recipes ) {
                const recipeId = recipe.dataset.recipeId;
                if ( recipeId ) {
                    WPRecipeMaker.conversion.setSystem( recipeId, system );
                }
            }

            if ( this.unitConversionChanger ) {
                const unitSystems = this.unitConversionChanger.querySelectorAll( '.wprm-unit-conversion' );
                for ( let unitSystem of unitSystems ) {
                    unitSystem.classList.remove( 'wprmpuc-active');

                    if ( system === parseInt( unitSystem.dataset.system ) ) {
                        unitSystem.classList.add( 'wprmpuc-active' );
                    }
                }
            }
        }
    },
    setInitialServings( recipes ) {
        // Need to do after timeout to make sure the servings have been initialized.
        setTimeout( () => {
            for ( let i = 0; i < recipes.length; i++ ) {
                let recipe = recipes[i];
    
                if ( recipe.servings && recipe.original_servings && recipe.servings !== recipe.original_servings ) {
                    WPRecipeMaker.quantities.setServings( recipe.id, recipe.servings );
                }
            }
        }, 100 );
    },
};
document.addEventListener( 'wprmPrintInit', () => {
    window.WPRMPremiumPrint.init();
} );