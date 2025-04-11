import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

import animateScrollTo from 'animated-scroll-to';
import '../../css/public/user-rating.scss';

window.WPRecipeMaker.userRating = {
	settings: {
		color: wprmp_public.settings.template_color_icon,
	},
	enter ( el ) {
		let color = window.WPRecipeMaker.userRating.settings.color;

		if ( 'modern' === wprmp_public.settings.recipe_template_mode && el.dataset.color ) {
			color = el.dataset.color;
		}

		// Fill current and previous.
		let prev = el;
		while ( prev ) {
			prev.classList.add( 'wprm-rating-star-selecting-filled' );

			const polygons = prev.querySelectorAll( 'polygon' );
			for ( let polygon of polygons ) {
				polygon.style.fill = color;
			}

			prev = prev.previousSibling;
		}

		// Get next.
		let next = el.nextSibling;
		while ( next ) {
			next.classList.add( 'wprm-rating-star-selecting-empty' );

			const polygons = next.querySelectorAll( 'polygon' );
			for ( let polygon of polygons ) {
				polygon.style.fill = 'none';
			}
		
			next = next.nextSibling;
		}
	},
	leave ( el ) {
		let star = el.parentNode.firstChild;

		while ( star ) {
			star.classList.remove( 'wprm-rating-star-selecting-filled' );
			star.classList.remove( 'wprm-rating-star-selecting-empty' );

			const polygons = star.querySelectorAll( 'polygon' );
			for ( let polygon of polygons ) {
				polygon.style.fill = '';
			}
		
			star = star.nextSibling;
		}
	},
	click ( el, e ) {
		const key = e.which || e.keyCode || 0;

		// Rate recipe on click, ENTER or SPACE.
		if ( 'click' === e.type || ( 13 === key || 32 === key ) ) {
			e.preventDefault();

			let allowUserRating = true;
			const container = el.parentNode;

			let rating = parseInt( el.dataset.rating );
			let recipeId = parseInt( container.dataset.recipe );

			// Backwards compatibility.
			if ( ! recipeId) {
				for ( var parent = el.parentNode; parent && parent != document; parent = parent.parentNode ) {
					if ( parent.matches( '.wprm-recipe-container' ) ) {
						recipeId = parseInt( parent.dataset.recipeId );
						break;
					}
				}
			}

			// Check if we allow a user rating.
			if ( wprmp_public.settings.features_comment_ratings && 'never' !== wprmp_public.settings.user_ratings_force_comment ) {
				const checkStars = {
					'1_star': 1,
					'2_star': 2,
					'3_star': 3,
					'4_star': 4,
					'always': 5,
				}

				if ( checkStars.hasOwnProperty( wprmp_public.settings.user_ratings_force_comment ) ) {
					if ( rating <= checkStars[ wprmp_public.settings.user_ratings_force_comment ] ) {
						allowUserRating = false;
					}
				}
			}

			if ( allowUserRating ) {
				window.WPRecipeMaker.userRating.rate( recipeId, el, container, rating );
			} else {
				window.WPRecipeMaker.userRating.commentRating( rating );
			}
		}
	},
	rate ( recipeId, starElement, container, rating ) {
		let decimals = 2;

		// Update current view.
		if ( container ) {
			let count = parseInt( container.dataset.count ),
				total = parseInt( container.dataset.total ),
				user = parseInt( container.dataset.user );

			if ( container.dataset.hasOwnProperty( 'decimals' ) ) {
				decimals = parseInt( container.dataset.decimals );
			}

			if ( user > 0 ) {
				total -= user;
			} else {
				count++;
			}

			total += rating;

			let average = Math.ceil( total / count * 100 ) / 100;
			average = Number( average.toFixed( decimals ) );

			// Upate details.
			const averageContainer = container.querySelector('.wprm-recipe-rating-average');
			const countContainer = container.querySelector('.wprm-recipe-rating-count');

			if ( averageContainer ) { averageContainer.innerText = average; }
			if ( countContainer ) { countContainer.innerText = count; }

			// Update stars.
			const stars = average;

			for ( let i = 1; i <= 5; i++ ) {
				let star = container.querySelector( '.wprm-rating-star-' + i );

				star.classList.remove( 'wprm-rating-star-full' );
				star.classList.remove( 'wprm-rating-star-empty' );
				star.classList.remove( 'wprm-rating-star-33' );
				star.classList.remove( 'wprm-rating-star-50' );
				star.classList.remove( 'wprm-rating-star-66' );

				if ( i <= stars ) {
					star.classList.add( 'wprm-rating-star-full' );
				} else {
					const difference = 0.0 + stars - i + 1;

					if ( 0 < difference && difference <= 0.33 ) {
						star.classList.add( 'wprm-rating-star-33' );
					} else if ( 0 < difference && difference <= 0.50 ) {
						star.classList.add( 'wprm-rating-star-50' );
					} else if ( 0 < difference && difference <= 0.66 ) {
						star.classList.add( 'wprm-rating-star-66' );
					} else if ( 0 < difference && difference <= 1 ) {
						star.classList.add( 'wprm-rating-star-full' );
					} else {
						star.classList.add( 'wprm-rating-star-empty' );
					}	
				}
			}
		}

		// Update rating via AJAX.
		const data = {
			action: 'wprm_user_rate_recipe',
			security: wprm_public.nonce,
			recipe_id: recipeId,
			post_id: wprm_public.post_id,
			rating: rating,
			decimals,
		};

		let request = new XMLHttpRequest();
		request.open( 'POST', wprm_public.ajax_url, true );
		request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );

		const params = Object.keys(data).map((k) => encodeURIComponent(k) + '=' + encodeURIComponent(data[k])).join('&');
		request.send( params );

		request.onreadystatechange = function() {
			if ( this.readyState == 4 && this.status == 200 ) {
				const data = JSON.parse( request.responseText );
				
				if ( data && data.hasOwnProperty( 'formatted' ) && data.formatted ) {
					const detailsContainer = container.querySelector( '.wprm-recipe-rating-details' );

					if ( detailsContainer ) {
						detailsContainer.innerHTML = data.formatted;
					}

					// Show thank you message.
					if ( wprmp_public.settings.user_ratings_thank_you_message ) {
						tippy( starElement, {
							trigger: 'manual',
							showOnCreate: true,
							content: wprmp_public.settings.user_ratings_thank_you_message,
							onShow(ref) {
								setTimeout(() => {
									ref.destroy();
								}, 3000);
							}
						});
					}
				}
			}
		}
	},
	commentRating( rating ) {
		// User rating not allowed, click on star in comment rating.
		const commentRatingContainer = document.querySelector('.comment-form-wprm-rating');

		if ( commentRatingContainer ) {
			const inputs = commentRatingContainer.querySelectorAll( 'input' );

			for ( let input of inputs ) {
				if ( rating === parseInt( input.value ) ) {
					input.click();
					break;
				}
			}
		}

		// Scroll to comment form.
		let scrollToElement = commentRatingContainer;

		if ( wprmp_public.settings.user_ratings_force_comment_scroll_to && ! ! document.querySelector( wprmp_public.settings.user_ratings_force_comment_scroll_to ) ) {
			scrollToElement = document.querySelector( wprmp_public.settings.user_ratings_force_comment_scroll_to );
		}

		if ( scrollToElement ) {
			animateScrollTo( scrollToElement, {
				verticalOffset: -100,
				speed: 250,
			} ).then(() => {
				const commentInput = document.getElementById('comment');
				if ( commentInput ) {
					commentInput.focus();
				}
			});
		}
	},
};