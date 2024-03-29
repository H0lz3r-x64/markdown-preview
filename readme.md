<img src="https://github.com/H0lz3r-x64/markdown-preview/blob/main/favicon.svg" width="50" align="right">

# Markdown Renderer

This project is a simple Markdown Renderer built with PHP and JavaScript. It allows you to input Markdown text directly or upload a Markdown file, and it will render the Markdown to HTML.

<img src="https://github.com/H0lz3r-x64/markdown-preview/assets/91200978/a967b43c-3f8c-462d-824c-5cb209b5ad95" width="1000">




## Features

- Input Markdown text directly into the text area
- Upload a Markdown file to be rendered
- Rendered Markdown is sanitized to prevent XSS attacks
- Copy the rendered HTML to clipboard
- Download the raw Markdown or the rendered HTML

## Usage

1. Clone the repository
2. Run `composer install` to install the required PHP dependencies
3. Start your PHP server and navigate to the project directory

## Dependencies

- [Parsedown](https://github.com/erusev/parsedown) for parsing Markdown to HTML
- [HTMLPurifier](http://htmlpurifier.org/) for sanitizing the HTML output
- [DOMPurify](https://github.com/cure53/DOMPurify) for sanitizing the HTML on the client side

## License

This project is licensed under the MIT License.
