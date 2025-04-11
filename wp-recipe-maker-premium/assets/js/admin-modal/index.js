import React from 'react';
const { hooks } = WPRecipeMaker['wp-recipe-maker/dist/shared'];

// General Modal.
import Nutrient from './nutrient';
import Nutrition from './nutrition';
import CustomField from './custom-field';

const premiumModal = {
    nutrient: Nutrient,
    nutrition: Nutrition,
    'custom-field': CustomField,
};

hooks.addFilter( 'modal', 'wp-recipe-maker', ( modal ) => {
    Object.keys( premiumModal ).map( ( id ) => {
        // Replace if exists, add otherwise.
        if ( modal.hasOwnProperty( id ) ) {
            modal[ id ] = premiumModal[ id ];
        } else {
            modal[ id ] = premiumModal[ id ];
        }
    });

    return modal;
} );


// Recipe Modal.
import EditEquipmentAffiliate from './recipe/edit-equipment-affiliate';
import EditIngredientLinks from './recipe/edit-ingredient-links';
import NutritionCalculation from './recipe/nutrition-calculation';

const premiumModalRecipe = {
    'equipment-affiliate': EditEquipmentAffiliate,
    'ingredient-links': EditIngredientLinks,
    'nutrition-calculation': NutritionCalculation,
};

hooks.addFilter( 'modalRecipe', 'wp-recipe-maker', ( modalRecipe ) => {
    Object.keys( premiumModalRecipe ).map( ( id ) => {
        // Replace if exists, add otherwise.
        if ( modalRecipe.hasOwnProperty( id ) ) {
            modalRecipe[ id ] = premiumModalRecipe[ id ];
        } else {
            modalRecipe[ id ] = premiumModalRecipe[ id ];
        }
    });

    return modalRecipe;
} );


// Recipe Ingredients Modal
// import UnitConversion from './recipe/ingredients/UnitConversion';
import IngredientLinks from './recipe/ingredients/IngredientLinks';

const premiumModalRecipeIngredients = {
    // 'unit-conversion': {
    //     block: UnitConversion,
    // },
    'ingredient-links': {
        block: IngredientLinks,
    },
};

hooks.addFilter( 'modalRecipeIngredients', 'wp-recipe-maker', ( modalRecipeIngredients ) => {
    Object.keys( premiumModalRecipeIngredients ).map( ( id ) => {
        // Merge if exists, add otherwise.
        if ( modalRecipeIngredients.hasOwnProperty( id ) ) {
            modalRecipeIngredients[ id ] = {
                ...modalRecipeIngredients[ id ],
                ...premiumModalRecipeIngredients[ id ],
            };
        } else {
            modalRecipeIngredients[ id ] = premiumModalRecipeIngredients[ id ];
        }
    });

    return modalRecipeIngredients;
} );

// Recipe Equipment Modal
import EquipmentAffiliate from './recipe/equipment/EquipmentAffiliate';

const premiumModalRecipeEquipment = {
    'equipment-affiliate': {
        block: EquipmentAffiliate,
    },
};

hooks.addFilter( 'modalRecipeEquipment', 'wp-recipe-maker', ( modalRecipeEquipment ) => {
    Object.keys( premiumModalRecipeEquipment ).map( ( id ) => {
        // Merge if exists, add otherwise.
        if ( modalRecipeEquipment.hasOwnProperty( id ) ) {
            modalRecipeEquipment[ id ] = {
                ...modalRecipeEquipment[ id ],
                ...premiumModalRecipeEquipment[ id ],
            };
        } else {
            modalRecipeEquipment[ id ] = premiumModalRecipeEquipment[ id ];
        }
    });

    return modalRecipeEquipment;
} );