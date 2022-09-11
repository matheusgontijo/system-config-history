const path = require('path');

function resolve(dir) {
    return path.join(__dirname, '..', dir);
}

module.exports = (baseConf) => {
    baseConf.config.module.rules.forEach((rule) => {
        if (rule.loader === 'url-loader') {
            if (!rule.exclude) {
                rule.exclude = [];
            }
            rule.exclude.push(resolve('src/app/assets/icons/svg'));
        }
    });

    return {
        module: {
            rules: [
                {
                    test: /\.svg$/,
                    include: [
                        resolve('src/app/assets/icons/svg'),
                    ],
                    loader: 'svg-inline-loader',
                    options: {
                        removeSVGTagAttrs: false,
                    },
                },
            ],
        },
    };
};
