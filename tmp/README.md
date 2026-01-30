# Virsh Online Logo - Embeddable Version

This directory contains the extracted logo from the Virsh Online website as a standalone, embeddable HTML file.

## File

- `logo.html` - Complete standalone logo with HTML, CSS, and JavaScript

## Features

✅ **Self-contained** - All styles and scripts embedded in a single HTML file  
✅ **Unique naming** - All classes/functions prefixed with `vo-logo-*` or `voLogo*` to avoid conflicts  
✅ **Animations** - Includes typing effect, glow animation, and symbol floating effects  
✅ **Theme support** - Automatic dark/light mode based on system preferences  
✅ **Responsive** - Adapts to different screen sizes  
✅ **Accessible** - Respects `prefers-reduced-motion` setting for users who prefer less animation  

## Usage

### As a Standalone Page
Simply open `logo.html` in a web browser.

### Embedded in Another Page
Copy and paste the content between `<body>` tags into your page:

```html
<!-- Virsh Online Logo - Standalone Embeddable Version -->
<div class="vo-logo-container">
    <div class="vo-logo-wrapper">
        <span class="vo-logo-symbol">※</span>
        <a href="#" class="vo-logo-type" id="vo-logo-text" data-text="ВІРШ ОНЛАЙН" aria-label="ВІРШ ОНЛАЙН">ВІРШ ОНЛАЙН</a>
        <span class="vo-logo-symbol">⸙</span>
    </div>
</div>
```

Then include the `<style>` and `<script>` sections from logo.html in your page's `<head>` and before closing `</body>` tag respectively.

### Via iframe
```html
<iframe src="path/to/logo.html" width="400" height="100" frameborder="0"></iframe>
```

## Customization

### Change Logo Text
Modify the `data-text` attribute:
```html
<a href="#" class="vo-logo-type" id="vo-logo-text" data-text="YOUR TEXT" aria-label="YOUR TEXT">YOUR TEXT</a>
```

### Change Link Target
Modify the `href` attribute:
```html
<a href="https://yoursite.com" class="vo-logo-type" ...>
```

### Disable Animation
Remove the `<script>` section or add this CSS:
```css
.vo-logo-type {
    animation: none !important;
}
```

### Force Light/Dark Theme
Add class to container:
```html
<div class="vo-logo-container vo-logo-theme-light">
<!-- or -->
<div class="vo-logo-container vo-logo-theme-dark">
```

## Technical Details

**Naming Convention:**
- CSS classes: `vo-logo-*` prefix (e.g., `vo-logo-container`, `vo-logo-wrapper`)
- JavaScript functions: `voLogo*` prefix (e.g., `voLogoInit`, `voLogoTypeWriter`)
- CSS variables: `--vo-logo-*` prefix (e.g., `--vo-logo-paper`, `--vo-logo-ink`)

**Dependencies:**
- Google Fonts: Special Elite (loaded via @import in CSS)

**Browser Support:**
- Modern browsers with CSS3 and ES6 support
- Fallback: Static text displayed without animation if JavaScript disabled
