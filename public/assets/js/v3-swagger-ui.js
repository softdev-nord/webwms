'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('swagger-ui');
    if (!container || typeof SwaggerUIBundle === 'undefined') {
        return;
    }

    SwaggerUIBundle({
        url: container.dataset.openapiUrl,
        dom_id: '#swagger-ui',
        deepLinking: true,
        displayRequestDuration: true,
        filter: true,
        persistAuthorization: true,
        validatorUrl: null,
        presets: [SwaggerUIBundle.presets.apis],
        layout: 'BaseLayout',
    });
});
