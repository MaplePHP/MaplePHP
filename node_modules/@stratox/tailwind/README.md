
# Stratox Tailwind CSS Theme
The Stratox Tailwind theme is an intuitive Tailwind CSS plugin featuring a range of polished UI components, including forms, buttons, containers, and headlines. It simplifies rem unit conversion, equating 1.5rem to 15px, and maintains a consistent CSS structure for ease of use.

## Installation

#### 1. Install the package
```
npm install @stratox/tailwind --save-dev
```

#### 2. Require the plugin package
Now you just have to require the `require('@stratox/tailwind');` plugin to the **tailwind.config.js** file.

```js
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/**/*.{html,js}"],
    theme: {
		extend: {},
	},
    plugins: [
        require('@stratox/tailwind').config({
        })
    ],
}
```
The theme has been installed!

## Configurations
Guide is not completed, more configs will come.

### Theme colors
```js
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/**/*.{html,js}"],
    theme: {
        extend: {
            colors: {
                'bg': {
                    'primary': "#1E40AF",
                    'secondary': "#E2E8F0",
                    'light': "#F9F9F9",
                    'approve': '#17A355',
                    'error': '#D32E32',
                },
                'text': {
                    'primary': "#0F172B",
                    'secondary': "#47566C",
                    'link': '#1E40AF',
                    'approve': '#17A355',
                    'error': '#D32E32',
                },
                'border': {
                    'primary': "#CDD5E0",
                    'secondary': "#70A3F3",
                    'light': "#E3E8EF",
                    'approve': '#4CA054',
                    'error': '#8D2822'
                },
            }
        }
    },
    plugins: [
        require('@stratox/tailwind').config({
        })
    ],
}
```

### Default font
Set default font.
```js
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/**/*.{html,js}"],
    theme: {
	    extend: {},
    },
    plugins: [
        require('@stratox/tailwind').config({
	        fontFamily: ['Helvetica', 'Arial', 'sans-serif'],
        })
    ],
}

```
### Custom font with @font-face
Install a custom font with @font-face.
```js
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./src/**/*.{html,js}"],
    theme: {
		extend: {},
	},
    plugins: [
        require('@stratox/tailwind').config({
	        fontFamily: ['Open Sans', 'Helvetica', 'Arial', 'sans-serif'],
	        fontFace: [
                {
                        'font-family': '"Open Sans"',
                        'src': 'url("fontface/opensans-bold-webfont.woff2") format("woff2")',
                        'font-weight': 'bold',
                        'font-style': 'normal',
                        'font-display': 'swap'
                },
                {
                        'font-family': '"Open Sans"',
                        'src': 'url("fontface/opensans-italic-webfont.woff2") format("woff2")',
                        'font-weight': 'normal',
                        'font-style': 'italic',
                        'font-display': 'swap'
                },
                {
                        'font-family': '"Open Sans"',
                        'src': 'url("fontface/opensans-regular-webfont.woff2") format("woff2")',
                        'font-weight': 'normal',
                        'font-style': 'normal',
                        'font-display': 'swap'
                }
            ]
        })
    ],
}
```
