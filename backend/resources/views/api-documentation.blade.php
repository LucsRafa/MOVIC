<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MOVIC API - Documentação</title>
    <link
      rel="stylesheet"
      href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css"
    />
    <link rel="stylesheet" href="{{ asset('swagger/movic-swagger-theme.css') }}" />
  </head>
  <body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script>
      window.onload = () => {
        SwaggerUIBundle({
          url: "/api/openapi.json",
          dom_id: "#swagger-ui",
          deepLinking: true,
          defaultModelsExpandDepth: -1,
          docExpansion: "none",
          tagsSorter: "alpha",
          operationsSorter: "alpha"
        });
      };
    </script>
  </body>
</html>
