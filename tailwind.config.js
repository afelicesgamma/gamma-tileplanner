module.exports = {
    content: ['./resources/**/*.{php,vue,js}', './General/**/*.php', './Admin/**/*.php'],
    theme: {
        fontFamily: {
            'sans': ['Inter', 'sans-serif'],
        },
        extend: {
            colors: {
                primary: 'var(--tileplanner-primary-color)'
            },
        },
    },
    plugins: [],
};
