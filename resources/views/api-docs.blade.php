<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>API Documentation — Deploy Manager</title>
  <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css" />
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <style>
    html {
      box-sizing: border-box;
      overflow: -grow-y;
    }
    *,
    *:before,
    *:after {
      box-sizing: inherit;
    }
    body {
      margin: 0;
      background: #0f172a; /* Slate 900 for dark mode */
    }
    /* Customize Swagger UI for slate theme */
    .swagger-ui {
      filter: invert(0) !important;
    }
    .swagger-ui .topbar {
      background-color: #1e293b;
      border-bottom: 1px solid #334155;
    }
    .swagger-ui .info .title {
      color: #f1f5f9 !important;
    }
    .swagger-ui .info p, .swagger-ui .info li, .swagger-ui .info td, .swagger-ui .info a {
      color: #cbd5e1 !important;
    }
    .swagger-ui .scheme-container {
      background: #1e293b !important;
      box-shadow: none !important;
      border-bottom: 1px solid #334155;
    }
    .swagger-ui select {
      background: #0f172a !important;
      color: #f1f5f9 !important;
      border-color: #475569 !important;
    }
    .swagger-ui .opblock-tag {
      color: #f1f5f9 !important;
      border-bottom: 1px solid #334155 !important;
    }
    .swagger-ui .opblock .opblock-summary-description {
      color: #cbd5e1 !important;
    }
    .swagger-ui .tabli button {
      color: #f1f5f9 !important;
    }
    .swagger-ui .response-col_status {
      color: #f1f5f9 !important;
    }
    .swagger-ui .response-col_links {
      color: #cbd5e1 !important;
    }
    .swagger-ui table thead tr td, .swagger-ui table thead tr th {
      color: #f1f5f9 !important;
      border-bottom: 1px solid #334155 !important;
    }
    .swagger-ui .parameter__name {
      color: #f1f5f9 !important;
    }
    .swagger-ui .parameter__type {
      color: #94a3b8 !important;
    }
    .swagger-ui .parameter__in {
      color: #64748b !important;
    }
    .swagger-ui .opblock-description-wrapper p, .swagger-ui .opblock-external-docs-wrapper p, .swagger-ui .opblock-title_normal p {
      color: #cbd5e1 !important;
    }
    .swagger-ui .opblock .opblock-section-header h4 {
      color: #f1f5f9 !important;
    }
    .swagger-ui .opblock .opblock-section-header {
      background: #1e293b !important;
      border-bottom: 1px solid #334155 !important;
    }
    .swagger-ui .opblock-body pre.microlight {
      background: #0f172a !important;
      border: 1px solid #334155 !important;
      color: #38bdf8 !important;
    }
    .swagger-ui .dialog-ux .modal-ux {
      background: #1e293b !important;
      border: 1px solid #475569 !important;
    }
    .swagger-ui .dialog-ux .modal-ux-header h3 {
      color: #f1f5f9 !important;
    }
    .swagger-ui .dialog-ux .modal-ux-content p {
      color: #cbd5e1 !important;
    }
    .swagger-ui .btn {
      background: #1e293b !important;
      color: #f1f5f9 !important;
      border-color: #475569 !important;
    }
    .swagger-ui .btn.execute {
      background: #4f46e5 !important;
      color: #ffffff !important;
      border: none !important;
    }
    .swagger-ui .btn.execute:hover {
      background: #4338ca !important;
    }
  </style>
</head>
<body>
  <div id="swagger-ui"></div>
  <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
  <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
  <script>
    window.onload = () => {
      window.ui = SwaggerUIBundle({
        spec: {
          openapi: "3.0.0",
          info: {
            title: "Deploy Manager API Documentation",
            version: "1.0.0",
            description: "Dokumentasi API resmi untuk Deploy Manager (Manajemen Deploy)."
          },
          servers: [
            {
              url: window.location.origin,
              description: "Server Aplikasi Saat Ini"
            }
          ],
          paths: {
            "/api/version": {
              get: {
                tags: ["Application Version"],
                summary: "Dapatkan versi aplikasi aktif",
                description: "Mendapatkan nomor versi aplikasi yang berjalan saat ini berdasarkan nama aplikasi atau API ID.",
                parameters: [
                  {
                    name: "name",
                    in: "query",
                    description: "Nama lengkap aplikasi di sistem",
                    required: false,
                    schema: {
                      type: "string",
                      example: "API Gateway"
                    }
                  },
                  {
                    name: "api_id",
                    in: "query",
                    description: "ID API aplikasi (eksternal/GUP)",
                    required: false,
                    schema: {
                      type: "integer",
                      example: 123
                    }
                  }
                ],
                responses: {
                  "200": {
                    description: "Berhasil mendapatkan versi aplikasi",
                    content: {
                      "application/json": {
                        schema: {
                          type: "object",
                          properties: {
                            success: {
                              type: "boolean",
                              example: true
                            },
                            application: {
                              type: "string",
                              example: "API Gateway"
                            },
                            version: {
                              type: "string",
                              example: "1.0.1"
                            },
                            updated_at: {
                              type: "string",
                              format: "date-time",
                              example: "2026-07-14T17:15:00+07:00"
                            }
                          }
                        }
                      }
                    }
                  },
                  "400": {
                    description: "Parameter tidak lengkap",
                    content: {
                      "application/json": {
                        schema: {
                          type: "object",
                          properties: {
                            success: {
                              type: "boolean",
                              example: false
                            },
                            message: {
                              type: "string",
                              example: "Parameter \"name\" atau \"api_id\" wajib diisi."
                            }
                          }
                        }
                      }
                    }
                  },
                  "404": {
                    description: "Aplikasi tidak ditemukan",
                    content: {
                      "application/json": {
                        schema: {
                          type: "object",
                          properties: {
                            success: {
                              type: "boolean",
                              example: false
                            },
                            message: {
                              type: "string",
                              example: "Aplikasi tidak ditemukan."
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        },
        dom_id: '#swagger-ui',
        deepLinking: true,
        presets: [
          SwaggerUIBundle.presets.apis,
          SwaggerUIStandalonePreset
        ],
        layout: "BaseLayout"
      });
    };
  </script>
</body>
</html>
