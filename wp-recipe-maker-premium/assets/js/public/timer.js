import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

import '../../css/public/timer.scss';

window.WPRecipeMaker.timer = {
    init: () => {
        // Not on print pages.
        const body = document.querySelector('body');
        if ( body && body.classList.contains( 'wprm-print' ) ) {
            return;
        }

        // Make timers a link.
        const timers = document.querySelectorAll( '.wprm-timer' );
        for ( let timer of timers ) {
            const seconds = timer.dataset.seconds;

            if ( seconds > 0 ) {
                let link = document.createElement('a');
                link.href = '#';
                link.classList.add( 'wprm-timer-link' );
                link.onclick = ( e ) => {
                    e.preventDefault();
                    e.stopPropagation();

                    window.WPRecipeMaker.timer.start( seconds );
                };

                timer.parentNode.insertBefore( link, timer );
                link.appendChild( timer );

                tippy( link, {
                    content: wprmp_public.timer.text.start_timer
                });
            }
        }
    },
    runningTimer: false,
    runningTotal: 0,
    runningRemaining: 0,
    lastUpdated: 0,
    update: () => {
        // Update remaining seconds.
        const elapsed = Date.now() - window.WPRecipeMaker.timer.lastUpdated;
        window.WPRecipeMaker.timer.runningRemaining -= elapsed;
        window.WPRecipeMaker.timer.lastUpdated = Date.now();

        // Check if finished and update display.
        const total = window.WPRecipeMaker.timer.runningTotal;
        let remaining = window.WPRecipeMaker.timer.runningRemaining;

        if( remaining <= 0 ) {
            remaining = 0;
            window.WPRecipeMaker.timer.finished();
        }

        window.WPRecipeMaker.timer.showTime( Math.round( remaining / 1000 ) );

        const percentage_elapsed = 100 * (total - remaining) / total;
        document.querySelector('#wprm-timer-bar-elapsed').style.width = percentage_elapsed + '%';
    },
	start: ( seconds ) => {
        window.WPRecipeMaker.timer.stop( () => {
            window.WPRecipeMaker.timer.createContainer();

            window.WPRecipeMaker.timer.runningTotal = seconds * 1000;
            window.WPRecipeMaker.timer.runningRemaining = seconds * 1000;
            window.WPRecipeMaker.timer.showTime( seconds );

            window.WPRecipeMaker.timer.play();
        });
    },
    play: () => {
        document.querySelector('#wprm-timer-play').style.display = 'none';
        document.querySelector('#wprm-timer-pause').style.display = '';
    
        if ( window.WPRecipeMaker.timer.interval ) {
            clearInterval( window.WPRecipeMaker.timer.interval );
        }
        window.WPRecipeMaker.timer.interval = setInterval( window.WPRecipeMaker.timer.update, 1000 );
        window.WPRecipeMaker.timer.lastUpdated = Date.now();
    },
    pauze: () => {
        document.querySelector('#wprm-timer-play').style.display = '';
        document.querySelector('#wprm-timer-pause').style.display = 'none';

        if ( window.WPRecipeMaker.timer.interval ) {
            clearInterval( window.WPRecipeMaker.timer.interval );
        }
    },
    stop: ( callback = false ) => {
        if ( window.WPRecipeMaker.timer.interval ) {
            clearInterval( window.WPRecipeMaker.timer.interval );
        }

        let container = document.querySelector( '#wprm-timer-container' );

        if ( container ) {
            container.parentNode.removeChild( container );
        }

        if ( callback ) {
            callback();
        }
    },
    finished: () => {
        window.WPRecipeMaker.timer.pauze();

        // Sound alarm once and keep pulsate background until closed.
        const alarm = new Audio( wprmp_public.timer.sound_file );
        alarm.play();

        document.querySelector( '#wprm-timer-container' ).classList.add( 'wprm-timer-finished' );
    },
    createContainer: () => {
        let container = document.createElement('div');
        container.id = 'wprm-timer-container';
        container.innerHTML = '<span id="wprm-timer-play" class="wprm-timer-icon" onclick="window.WPRecipeMaker.timer.play()">' + wprmp_public.timer.icons.play + '</span>';
        container.innerHTML += '<span id="wprm-timer-pause" class="wprm-timer-icon" onclick="window.WPRecipeMaker.timer.pauze()">' + wprmp_public.timer.icons.pause + '</span>';
        container.innerHTML += '<span id="wprm-timer-remaining"></span>';
        container.innerHTML += '<span id="wprm-timer-bar-container"><span id="wprm-timer-bar"><span id="wprm-timer-bar-elapsed"></span></span></span>';
        container.innerHTML += '<span id="wprm-timer-close" class="wprm-timer-icon" onclick="window.WPRecipeMaker.timer.stop()">' + wprmp_public.timer.icons.close + '</span>';

        document.querySelector('body').appendChild(container);
    },
    showTime: ( s ) => {
        var h = Math.floor(s/3600);
        s -= h*3600;
        var m = Math.floor(s/60);
        s -= m*60;
        const hms = (h < 10 ? '0'+h : h)+":"+(m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s);

        document.querySelector('#wprm-timer-remaining').textContent = hms;
    },
};

ready(() => {
    window.WPRecipeMaker.timer.init();
});

function ready( fn ) {
    if (document.readyState != 'loading'){
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}