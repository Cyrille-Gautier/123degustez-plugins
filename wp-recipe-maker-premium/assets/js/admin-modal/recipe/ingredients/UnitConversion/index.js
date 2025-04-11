import React, { Component } from 'react';

import '../../../../../css/admin/modal/recipe/unit-conversion.scss';

import Api from 'Shared/Api';
import { __wprm } from 'Shared/Translations';
import  { parseQuantity, formatQuantity } from '../../../../shared/quantities';

import FieldDropdown from 'Modal/fields/FieldDropdown';
import UnitConversionIngredient from './UnitConversionIngredient';

export default class UnitConversion extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isConverting: {},
            methods: {},
        }

        this.convert = this.convert.bind(this);
        this.convertAll = this.convertAll.bind(this);
    }

    convertAll( method, system = 'default' ) {
        let indexesToConvert = [];

        for ( let i = 0; i < this.props.ingredients.length; i++ ) {
            const ingredient = this.props.ingredients[ i ];

            if ( 'ingredient' === ingredient.type ) {
                indexesToConvert.push( i );
            }
        }

        if ( indexesToConvert ) {
            this.convert( indexesToConvert, method, system );
        }
    }

    convert( indexes, method, system = 'default' ) {
        let isConverting = this.state.isConverting;
        let methods = this.state.methods;

        if ( 'none' === method ) {
            let newIngredients = JSON.parse( JSON.stringify( this.props.ingredients ) );

            for ( let index of indexes ) {
                const ingredient = this.props.ingredients[ index ];

                if ( ! ingredient.hasOwnProperty('converted') ) {
                    newIngredients[ index ].converted = { 2: {} };
                }

                newIngredients[ index ].converted[2].amount = ingredient.amount;
                newIngredients[ index ].converted[2].unit = ingredient.unit;

                isConverting[ index ] = false;
                methods[ index ] = method;
            }

            this.props.onIngredientsChange(newIngredients);
        } else {
            let ingredientsToConvert = {};

            for ( let index of indexes ) {
                const ingredient = this.props.ingredients[ index ];

                ingredientsToConvert[ index ] = {
                    index,
                    amount: parseQuantity( ingredient.amount ),
                    unit: ingredient.unit,
                    name: ingredient.name,
                };

                // Force conversion to specific unit.
                if ( 'automatic' !== method ) {
                    ingredientsToConvert[ index ].units_to = [ method ];
                }

                isConverting[ index ] = true;
                methods[ index ] = method;
            }

            Api.unitConversion.get( ingredientsToConvert, system ).then((data) => {
                if ( data && data.conversions ) {
                    let newIngredients = JSON.parse( JSON.stringify( this.props.ingredients ) );
                    let isConverting = this.state.isConverting;
                    let methods = this.state.methods;

                    for ( let index in data.conversions ) {
                        const ingredient = this.props.ingredients[ parseInt( index ) ];
                        const conversion = data.conversions[ index ];

                        if ( ! ingredient.hasOwnProperty('converted') ) {
                            newIngredients[ index ].converted = { 2: {} };
                        }

                        if ( 'failed' === conversion.type ) {
                            newIngredients[ index ].converted[2].amount = ingredient.amount;
                            newIngredients[ index ].converted[2].unit = ingredient.unit;
                            methods[ index ] = conversion.type;
                        } else {
                            newIngredients[ index ].converted[2].amount = formatQuantity( conversion.amount, wprmp_admin.settings.unit_conversion_round_to_decimals, true );
                            newIngredients[ index ].converted[2].unit = conversion.alias;
                            methods[ index ] = 'none' === conversion.type ? 'none' : method;
                        }

                        isConverting[ index ] = false;
                    }

                    // Update ingredient and state.
                    this.props.onIngredientsChange(newIngredients);
                    this.setState({
                        isConverting,
                        methods,
                    });
                }
            });
        }

        this.setState({
            isConverting,
            methods,
        });
    }

    render() {
        if ( ! wprm_admin.addons.pro ) {
            return (
                <p>{ __wprm( 'This feature is only available in' ) } <a href="https://bootstrapped.ventures/wp-recipe-maker/get-the-plugin/" target="_blank">WP Recipe Maker Pro Bundle</a>.</p>
            );
        }

        if ( ! wprm_admin_modal.unit_conversion ) {
            return (
                <p>{ __wprm( 'You need to set up this feature on the WP Recipe Maker > Settings > Unit Conversion page first.' ) }</p>
            );
        }

        const ingredients = this.props.ingredients.filter((field) => 'ingredient' === field.type && field.name );
        if ( ! ingredients.length ) {
            return (
                <p>{ __wprm( 'No ingredients set for this recipe.' ) }</p>
            );
        }

        // Get Unit System for the recipe.
        let recipeUnitSystem = 'default' === this.props.system ? wprm_admin_modal.unit_conversion.default_system : this.props.system;
        recipeUnitSystem = parseInt( recipeUnitSystem );
        
        // Prevent issues.
        if ( recipeUnitSystem !== 1 && recipeUnitSystem !== 2 ) {
            recipeUnitSystem = 1;
        }
        const convertedUnitSystem = 2 === recipeUnitSystem ? 1 : 2;
    
        return (
            <div
                className="wprm-admin-modal-field-ingredient-unit-conversion-container"
            >
                <div
                    className="wprm-admin-modal-field-ingredient-unit-conversion-system"
                >
                    <label>{ __wprm( 'Original Unit System for this recipe' ) }:</label>
                    <FieldDropdown
                        options={ [
                            {
                                value: 'default',
                                label: `${ __wprm( 'Use Default' ) } (${ wprm_admin_modal.unit_conversion.systems[ parseInt( wprm_admin_modal.unit_conversion.default_system ) ].label })`,
                            },
                            {
                                value: '1',
                                label: `${ __wprm( 'First Unit System' ) } (${ wprm_admin_modal.unit_conversion.systems[1].label })`,
                            },
                            {
                                value: '2',
                                label: `${ __wprm( 'Second Unit System' ) } (${ wprm_admin_modal.unit_conversion.systems[2].label })`,
                            },
                        ] }
                        value={ this.props.system }
                        onChange={ (system) => {
                            this.props.onSystemChange( system );
                        }}
                        width={ 255 }
                    />
                </div>
                <table
                    className="wprm-admin-modal-field-ingredient-unit-conversion"
                >
                    <thead>
                    <tr>
                        <th>{ __wprm( 'Conversion' ) }</th>
                        <th>{ __wprm( 'Converted' ) } ({ wprm_admin_modal.unit_conversion.systems[ convertedUnitSystem ].label })</th>
                        <th>{ __wprm( 'Original' ) } ({ wprm_admin_modal.unit_conversion.systems[ recipeUnitSystem ].label })</th>
                    </tr>
                    </thead>
                    <tbody>
                    {
                        this.props.ingredients.map((field, index) => {
                            if ( 'group' === field.type || ! field.name ) {
                                return null;
                            }
        
                            return (
                                <UnitConversionIngredient
                                    ingredient={ field }
                                    isConverting={ this.state.isConverting[ index ] }
                                    method={ this.state.methods[ index ] }
                                    onMethodChange={(method) => {
                                        if ( ! this.state.isConverting[ index ] ) {
                                            this.convert( [ index ], method, convertedUnitSystem );
                                        }
                                    }}
                                    onConvertedChange={(converted) => {
                                        let newIngredients = JSON.parse( JSON.stringify( this.props.ingredients ) );
                                        newIngredients[ index ].converted = converted;

                                        this.props.onIngredientsChange(newIngredients);
                                    }}
                                    convertedUnitSystem={ convertedUnitSystem }
                                    key={ index }
                                />
                            )
                        })
                    }
                    </tbody>
                </table>
                <button
                    className="button button-primary"
                    onClick={(e) => {
                        e.preventDefault();
                        this.convertAll( 'automatic', convertedUnitSystem );
                    } }
                >{ __wprm( 'Convert All Automatically' ) }</button>
            </div>
        );
    }
}