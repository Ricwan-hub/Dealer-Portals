import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                secondary: {
                    '50': '#f6f7f8',
                    '100': '#eaedef',
                    '200': '#dde2e6',
                    '300': '#c0cad0',
                    '400': '#a1afb9',
                    '500': '#8a98a7',
                    '600': '#788698',
                    '700': '#6c7689',
                    '800': '#5b6372',
                    '900': '#4c525c',
                    '950': '#31343a',
                },               
            },

        },

    },
}
