import config from '../tailwindConfig';

// The above import is a very selective generation of the tailwind config
// If there are keys from the config you need and are not present, 
// edit generateFrontendTailwindConfig.js to add them in the 
// "allowedThemeKeys" array.

export default function useTailwindConfig() {
    return config;
}