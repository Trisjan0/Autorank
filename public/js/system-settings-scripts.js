document.addEventListener('DOMContentLoaded', () => {
    // References for toggling dark mode
    const darkModeToggle = document.getElementById('darkModeToggle');

    // References for changing color scheme
    const primaryColorInput = document.getElementById('primaryColor');

    // Reference for reset custom color changes
    const resetColorsBtn = document.getElementById('resetColorsBtn');

    // References for default colors
    const defaultColors = {
        // Primary Color
        '--primaryColor': 'hsl(var(--base-hue-primary), var(--base-saturation-primary), var(--base-lightness-primary))',

        /* --- Website Colors --- */
        '--pageBackgroundColor': '#ffffff',
        '--pageTextColor': '#000000',
        '--pageTextColorOnBlack': '#ffffff',

        /* --- Card Colors --- */
        '--cardBackgroundColorL': '#ffffff',
        '--cardBorderColorL': '#262626',

        /* --- Progress Bar Colors --- */
        '--progressBarEmptyColor': '#d3d3d3',
        '--progressBarFillColor': '#4DFF62',

        /* --- Table Colors --- */
        '--tableBackgroundColor': '#ffffff',
        '--tableDataBorderColor': '#d3d3d3',
    };

    // References for dark mode colors
    const darkModeColors = {
        // Primary Color
        '--primaryColor': 'hsl(var(--base-hue-primary), var(--base-saturation-primary), var(--base-lightness-primary))',

        /* --- Website Colors --- */
        '--pageBackgroundColor': '#191919',
        '--pageTextColor': '#ffffff',
        '--pageTextColorOnBlack': '#ffffffff',

        /* --- Card Colors --- */
        '--cardBackgroundColorL': '#363636',
        '--cardBorderColorL': '#262626',

        /* --- Progress Bar Colors --- */
        '--progressBarEmptyColor': '#404040',
        '--progressBarFillColor': '#299e40',

        /* --- Table Colors --- */
        '--tableBackgroundColor': '#333333',
        '--tableDataBorderColor': '#434343',
    };

    // Loads and applies custom color scheme based on save state
    loadAndApplyCustomColors();

    /*
    |--------------------------------------------------------------------------
    | FOR TOGGLING DARK MODE -- START
    |--------------------------------------------------------------------------
    */
    // Load Dark Mode preference
    const savedDarkModeState = localStorage.getItem('darkModeEnabled');
    const isDarkModeEnabled = savedDarkModeState === 'true'; // saved as string

    // Apply dark mode class immediately based on saved state
    if (isDarkModeEnabled) {
        document.body.classList.add('dark-mode');
    } else {
        document.body.classList.remove('dark-mode');
    }

    // If the toggle element exists on this page, set its checked state
    if (darkModeToggle) {
        darkModeToggle.checked = isDarkModeEnabled;
    }

    function toggleDarkMode(isDarkMode) {
    if (isDarkMode) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkModeEnabled', 'true');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkModeEnabled', 'false');
        }
        loadAndApplyCustomColors();
    }

    // Event listener for the dark mode toggle
    if (darkModeToggle) {
        darkModeToggle.addEventListener('change', (event) => {
            toggleDarkMode(event.target.checked);
        });
    }
    /*
    |--------------------------------------------------------------------------
    | FOR TOGGLING DARK MODE -- END
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | FOR CHANGING COLOR SCHEME -- START
    |--------------------------------------------------------------------------
    */
    /**
     * Applies the given color values to the CSS custom properties and updates the input fields.
     * This function directly sets the properties on the documentElement (which represents :root),
     * ensuring user selections override any theme-based defaults.
     * @param {object} colors - An object containing CSS variable names as keys and color values as values.
     */
    function applyColors(colors) {
    for (const [prop, value] of Object.entries(colors)) {
        document.documentElement.style.setProperty(prop, value);
        // Update the corresponding input field value based on the CSS variable name
        if (prop === '--primaryColor' && primaryColorInput) primaryColorInput.value = value;
        }
    }

    /**
     * Saves a single color property to localStorage and applies it to the CSS.
     * @param {string} cssVarName - The name of the CSS custom property (e.g., '--primaryColor').
     * @param {string} value - The color value (e.g., '#RRGGBB' or 'hsl(...)').
     */
    function saveAndApplyColor(cssVarName, value) {
        document.documentElement.style.setProperty(cssVarName, value);
        localStorage.setItem(cssVarName, value);
    }

    /**
     * Loads custom colors from localStorage and applies them.
     * These will override the current theme's default colors.
     */
    function loadAndApplyCustomColors() {
        const currentCustomColors = {};
        // Iterate through the default light theme colors to get the variable names
        // This ensures we check localStorage for all relevant color properties
        for (const prop in defaultColors) { // Use defaultLightColors to get all variable names
            const savedValue = localStorage.getItem(prop);
            if (savedValue) {
                currentCustomColors[prop] = savedValue;
            }
        }
        // Apply only the colors that were explicitly saved by the user
        applyColors(currentCustomColors);
    }

    // Add event listeners to each color input
    // Check if element exists before adding listener, as these might only be on the settings page
    if (primaryColorInput) {
        primaryColorInput.addEventListener('input', (event) => {
            saveAndApplyColor('--primaryColor', event.target.value);
        });
    }

    // Event listener for the reset button
    if (resetColorsBtn) {
        resetColorsBtn.addEventListener('click', () => {
            // Determine which default set to apply based on current dark mode state
            const currentDefaults = darkModeToggle.checked ? darkModeColors : defaultColors;
            applyColors(currentDefaults);

            // Clear all custom color settings from localStorage
            // We iterate through defaultLightColors to ensure all possible custom color properties are cleared
            for (const prop in defaultColors) {
                localStorage.removeItem(prop);
            }
        });
    }
    /*
    |--------------------------------------------------------------------------
    | FOR CHANGING COLOR SCHEME -- END
    |--------------------------------------------------------------------------
    */
});
