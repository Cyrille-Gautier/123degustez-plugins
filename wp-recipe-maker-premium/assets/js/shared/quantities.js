import frac from 'frac';

const fractionSymbolsMap = {
	'\u00BC': '1/4', '\u00BD': '1/2', '\u00BE': '3/4', '\u2150': '1/7',
	'\u2151': '1/9', '\u2152': '1/10', '\u2153': '1/3', '\u2154': '2/3',
	'\u2155': '1/5', '\u2156': '2/5', '\u2157': '3/5', '\u2158': '4/5',
	'\u2159': '1/6', '\u215A': '5/6', '\u215B': '1/8', '\u215C': '3/8',
	'\u215D': '5/8', '\u215E': '7/8'
};

export function formatQuantity( quantity, decimals = 2, allowFractions = false ) {
	let formattedQuantity = quantity;
	let displayAsFraction = false;

	// Check if fractions are enabled.
	if ( allowFractions ) {
		const fractionsEnabled = typeof window.wprmp_public !== 'undefined' ? wprmp_public.settings.fractions_enabled : wprmp_admin.settings.fractions_enabled;

		if ( fractionsEnabled ) {
			const useMixed = typeof window.wprmp_public !== 'undefined' ? wprmp_public.settings.fractions_use_mixed : wprmp_admin.settings.fractions_use_mixed;
			const useSymbols = typeof window.wprmp_public !== 'undefined' ? wprmp_public.settings.fractions_use_symbols : wprmp_admin.settings.fractions_use_symbols;
			let maxDenominator = parseInt( typeof window.wprmp_public !== 'undefined' ? wprmp_public.settings.fractions_max_denominator : wprmp_admin.settings.fractions_max_denominator );
			maxDenominator = maxDenominator > 1 ? maxDenominator : 8;
			
			const fractionParts = frac( quantity, maxDenominator, useMixed );

			if ( fractionParts && 3 === fractionParts.length && ! isNaN( fractionParts[0] ) && ! isNaN( fractionParts[1] ) && ! isNaN( fractionParts[2] ) ) {
				let formattedFraction = '';

				if ( 0 < fractionParts[0] ) {
					formattedFraction += `${fractionParts[0]} `;
				}

				if ( 0 < fractionParts[1] ) {
					if ( 0 < fractionParts[2] ) {
						formattedFraction += 1 === fractionParts[2] ? fractionParts[1] : `${fractionParts[1]}/${fractionParts[2]}`;
					}
				} else {
					// End result should not be 0.
					if ( 0 === fractionParts[0] ) {
						formattedFraction += `1/${maxDenominator}`;
					}
				}

				if ( formattedFraction ) {
					if ( useSymbols ) {
						formattedFraction = ' ' + formattedFraction + ' ';

						for ( let symbol of Object.keys( fractionSymbolsMap ) ) {
							const fraction = fractionSymbolsMap[ symbol ];

							formattedFraction = formattedFraction.replace( ` ${fraction} `, ` ${symbol} ` );
						}
					}

					formattedQuantity = formattedFraction.trim();
					displayAsFraction = true;
				}
			}
		}
	}

	// Not using fractions, round to x decimals.
	if ( ! displayAsFraction ) {
		decimals = parseInt(decimals);

		// Round to x decimals, but prevent 0 values.
		do {
			formattedQuantity = parseFloat(parseFloat(quantity).toFixed(decimals));
			decimals++;

			// Prevent infinite loop. Use 4 decimals as max precision.
			if ( 3 < decimals ) {
				break;
			}
		} while ( 0.0 == formattedQuantity )

		// Make string again.
		formattedQuantity = '' + formattedQuantity;

		// Optionally use comma as decimal separator (point is default).
		const decimalSeparator = typeof window.wprmp_public !== 'undefined' ? wprmp_public.settings.decimal_separator : wprmp_admin.settings.decimal_separator;

		if ( 'comma' === decimalSeparator ) {
			formattedQuantity = formattedQuantity.replace('.', ',');
		}
	}

	return formattedQuantity;
}

export function parseQuantity(sQuantity) {
	// Make sure sQuantity is a string.
	sQuantity = '' + sQuantity;

	// Use . for decimals
	sQuantity = sQuantity.replace(',', '.');

	// Replace " to " by dash.
	sQuantity = sQuantity.replace(' to ', '-');

	// Replace fraction characters with equivalent
	var fractionsRegex = /(\u00BC|\u00BD|\u00BE|\u2150|\u2151|\u2152|\u2153|\u2154|\u2155|\u2156|\u2157|\u2158|\u2159|\u215A|\u215B|\u215C|\u215D|\u215E)/;
	sQuantity = (sQuantity + '').replace(fractionsRegex, function(m, vf) {
		return ' ' + fractionSymbolsMap[vf];
	});

	// Strip HTML tags.
	sQuantity = sQuantity.replace( /(<([^>]+)>)/ig, '' );

	// Strip shortcodes.
	sQuantity = sQuantity.replace( /(\[([^\]]+)\])/ig, '' );

	// Replace leftover characters we're not expecting by spaces.
	sQuantity = sQuantity.replace( /[^\d\s\.\/-]/ig, '' );

	// Split by spaces
	sQuantity = sQuantity.trim();
	var parts = sQuantity.split(' ');

	var quantity = false;

	if(sQuantity !== '') {
		quantity = 0;

		// Loop over parts and add values
		for(var i = 0; i < parts.length; i++) {
			if(parts[i].trim() !== '') {
				var division_parts = parts[i].split('/', 2);
				var part_quantity = parseFloat(division_parts[0]);

				if(division_parts[1] !== undefined) {
					var divisor = parseFloat(division_parts[1]);

					if(divisor !== 0) {
						part_quantity /= divisor;
					}
				}

				if ( ! isNaN( part_quantity ) ) {
					quantity += part_quantity;
				}
			}			
		}
	}

	return quantity;
}