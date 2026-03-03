import preset from '../../../../vendor/filament/filament/tailwind.config.preset'
import typography from '@tailwindcss/typography';
import forms from '@tailwindcss/forms';

export default {
    presets: [preset],
    content: [
        './app/Filament/Dealer/**/*.php',
        './resources/views/filament/dealer/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter'],
            }
        }
    },

    plugins: [
        forms, 
        typography,
    ],
}
