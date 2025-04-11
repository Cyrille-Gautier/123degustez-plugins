const { hooks } = WPRecipeMaker['wp-recipe-maker/dist/shared'];

import Collection from './Collection';
import CustomField from './CustomField';
import CustomTaxonomy from './CustomTaxonomy';
import EquipmentAffiliate from './EquipmentAffiliate';
import IngredientLinks from './IngredientLinks';
import Nutrient from './Nutrient';
import Nutrition from './Nutrition';
import Submission from './Submission';
import UnitConversion from './UnitConversion';

const premiumApi = {
    collection: Collection,
    customField: CustomField,
    customTaxonomy: CustomTaxonomy,
    equipmentAffiliate: EquipmentAffiliate,
    ingredientLinks: IngredientLinks,
    nutrient: Nutrient,
    nutrition: Nutrition,
    submission: Submission,
    unitConversion: UnitConversion,
};

hooks.addFilter( 'api', 'wp-recipe-maker', ( api ) => {
    Object.keys( premiumApi ).map( ( id ) => {
        // Merge if exists, add otherwise.
        if ( api.hasOwnProperty( id ) ) {
            api[ id ] = {
                ...api[ id ],
                ...premiumApi[ id ],
            };
        } else {
            api[ id ] = premiumApi[ id ];
        }
    });

    return api;
} );