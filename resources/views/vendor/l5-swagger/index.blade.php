<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $documentationTitle }}</title>
    <link rel="stylesheet" type="text/css" href="{{ l5_swagger_asset($documentation, 'swagger-ui.css') }}">
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'favicon-16x16.png') }}" sizes="16x16"/>
    <style>
    html
    {
        box-sizing: border-box;
        overflow: -moz-scrollbars-vertical;
        overflow-y: scroll;
    }
    *,
    *:before,
    *:after
    {
        box-sizing: inherit;
    }

    body {
      margin:0;
      background: #fafafa;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    /* Changelog Tabs */
    .changelog-tabs { background: white; border-bottom: 2px solid #dee2e6; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .changelog-tabs ul { list-style: none; display: flex; margin: 0; padding: 0; }
    .changelog-tabs li { margin: 0; }
    .changelog-tabs button { background: none; border: none; padding: 16px 28px; font-size: 15px; font-weight: 600; color: #6c757d; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s; }
    .changelog-tabs button:hover { color: #495057; background: #f8f9fa; }
    .changelog-tabs button.active { color: #0d6efd; border-bottom-color: #0d6efd; background: #e7f1ff; }
    
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }
    
    /* Changelog Container */
    .changelog-wrapper { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
    .changelog-hero { text-align: center; margin-bottom: 40px; padding: 50px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white; box-shadow: 0 8px 20px rgba(102,126,234,0.3); }
    .changelog-hero h1 { font-size: 38px; font-weight: 700; margin: 0 0 10px; }
    .changelog-hero p { font-size: 16px; margin: 5px 0; opacity: 0.95; }
    .last-update { margin-top: 15px; font-size: 14px; font-weight: 500; }
    .repo-link { display: inline-block; margin-top: 15px; padding: 8px 18px; background: rgba(255,255,255,0.2); border-radius: 20px; color: white; text-decoration: none; font-weight: 600; transition: all 0.2s; }
    .repo-link:hover { background: rgba(255,255,255,0.3); }
    .stats-badges { display: flex; gap: 12px; justify-content: center; margin-top: 20px; flex-wrap: wrap; }
    .badge-pill { padding: 6px 15px; background: rgba(255,255,255,0.25); border-radius: 15px; font-size: 13px; font-weight: 600; }
    
    /* Loading */
    .loading-box { text-align: center; padding: 60px 20px; }
    .spinner { width: 40px; height: 40px; border: 4px solid #e9ecef; border-top-color: #0d6efd; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 15px; }
    @keyframes spin { to { transform: rotate(360deg); } }
    
    /* Commit Cards */
    .commits-list { display: grid; gap: 20px; margin-top: 30px; }
    .commit-card { background: white; border-radius: 10px; padding: 24px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); border-left: 4px solid #0d6efd; transition: all 0.3s; }
    .commit-card:hover { box-shadow: 0 6px 16px rgba(0,0,0,0.12); transform: translateY(-3px); }
    .commit-head { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; }
    .avatar { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #dee2e6; }
    .avatar.fallback { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 16px; }
    .author-info { flex: 1; }
    .author-name { font-weight: 700; color: #212529; font-size: 15px; margin: 0; }
    .commit-time { font-size: 12px; color: #6c757d; margin: 2px 0 0; }
    .sha-badge { font-family: 'Consolas', monospace; background: #f1f3f5; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #495057; border: 1px solid #dee2e6; }
    .commit-msg { margin: 15px 0; }
    .commit-title { font-size: 16px; font-weight: 600; color: #212529; line-height: 1.5; }
    .type-label { display: inline-block; padding: 4px 10px; border-radius: 5px; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-right: 8px; letter-spacing: 0.3px; }
    .type-label.feat { background: #d1f4e0; color: #0f5132; }
    .type-label.fix { background: #f8d7da; color: #842029; }
    .type-label.docs { background: #cfe2ff; color: #084298; }
    .type-label.refactor { background: #e7d6f8; color: #5a1a86; }
    .type-label.chore { background: #e9ecef; color: #495057; }
    .type-label.test { background: #fff3cd; color: #856404; }
    .commit-desc { color: #6c757d; font-size: 13px; line-height: 1.5; margin-top: 6px; }
    .commit-stats { display: flex; gap: 15px; padding-top: 12px; margin-top: 12px; border-top: 1px solid #e9ecef; font-size: 12px; }
    .stat { display: flex; align-items: center; gap: 5px; font-weight: 600; }
    .stat.add { color: #198754; }
    .stat.del { color: #dc3545; }
    .stat.tot { color: #6c757d; }
    .commit-foot { margin-top: 15px; text-align: right; }
    .view-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #0d6efd; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: all 0.2s; }
    .view-btn:hover { background: #0b5ed7; }
    .view-btn svg { width: 14px; height: 14px; }
    
    /* Error */
    .error-box { text-align: center; padding: 60px 20px; }
    .error-icon { font-size: 60px; margin-bottom: 15px; }
    .error-title { font-size: 20px; font-weight: 700; color: #dc3545; margin-bottom: 8px; }
    .error-msg { font-size: 14px; color: #6c757d; }
    
    @media (max-width: 768px) {
        .changelog-hero h1 { font-size: 28px; }
        .commit-card { padding: 16px; }
        .changelog-tabs button { padding: 12px 18px; font-size: 14px; }
    }
    </style>
    @if(config('l5-swagger.defaults.ui.display.dark_mode'))
        <style>
            body#dark-mode,
            #dark-mode .scheme-container {
                background: #1b1b1b;
            }
            #dark-mode .scheme-container,
            #dark-mode .opblock .opblock-section-header{
                box-shadow: 0 1px 2px 0 rgba(255, 255, 255, 0.15);
            }
            #dark-mode .operation-filter-input,
            #dark-mode .dialog-ux .modal-ux,
            #dark-mode input[type=email],
            #dark-mode input[type=file],
            #dark-mode input[type=password],
            #dark-mode input[type=search],
            #dark-mode input[type=text],
            #dark-mode textarea{
                background: #343434;
                color: #e7e7e7;
            }
            #dark-mode .title,
            #dark-mode li,
            #dark-mode p,
            #dark-mode table,
            #dark-mode label,
            #dark-mode .opblock-tag,
            #dark-mode .opblock .opblock-summary-operation-id,
            #dark-mode .opblock .opblock-summary-path,
            #dark-mode .opblock .opblock-summary-path__deprecated,
            #dark-mode h1,
            #dark-mode h2,
            #dark-mode h3,
            #dark-mode h4,
            #dark-mode h5,
            #dark-mode .btn,
            #dark-mode .tab li,
            #dark-mode .parameter__name,
            #dark-mode .parameter__type,
            #dark-mode .prop-format,
            #dark-mode .loading-container .loading:after{
                color: #e7e7e7;
            }
            #dark-mode .opblock-description-wrapper p,
            #dark-mode .opblock-external-docs-wrapper p,
            #dark-mode .opblock-title_normal p,
            #dark-mode .response-col_status,
            #dark-mode table thead tr td,
            #dark-mode table thead tr th,
            #dark-mode .response-col_links,
            #dark-mode .swagger-ui{
                color: wheat;
            }
            #dark-mode .parameter__extension,
            #dark-mode .parameter__in,
            #dark-mode .model-title{
                color: #949494;
            }
            #dark-mode table thead tr td,
            #dark-mode table thead tr th{
                border-color: rgba(120,120,120,.2);
            }
            #dark-mode .opblock .opblock-section-header{
                background: transparent;
            }
            #dark-mode .opblock.opblock-post{
                background: rgba(73,204,144,.25);
            }
            #dark-mode .opblock.opblock-get{
                background: rgba(97,175,254,.25);
            }
            #dark-mode .opblock.opblock-put{
                background: rgba(252,161,48,.25);
            }
            #dark-mode .opblock.opblock-delete{
                background: rgba(249,62,62,.25);
            }
            #dark-mode .loading-container .loading:before{
                border-color: rgba(255,255,255,10%);
                border-top-color: rgba(255,255,255,.6);
            }
            #dark-mode svg:not(:root){
                fill: #e7e7e7;
            }
            #dark-mode .opblock-summary-description {
                color: #fafafa;
            }
        </style>
    @endif
</head>

<body @if(config('l5-swagger.defaults.ui.display.dark_mode')) id="dark-mode" @endif>
<div id="swagger-ui"></div>

<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-bundle.js') }}"></script>
<script src="{{ l5_swagger_asset($documentation, 'swagger-ui-standalone-preset.js') }}"></script>
<script>
    window.onload = function() {
        const urls = [];

        @foreach($urlsToDocs as $title => $url)
            urls.push({name: "{{ $title }}", url: "{{ $url }}"});
        @endforeach

        // Build a system
        const ui = SwaggerUIBundle({
            dom_id: '#swagger-ui',
            urls: urls,
            "urls.primaryName": "{{ $documentationTitle }}",
            operationsSorter: {!! isset($operationsSorter) ? '"' . $operationsSorter . '"' : 'null' !!},
            configUrl: {!! isset($configUrl) ? '"' . $configUrl . '"' : 'null' !!},
            validatorUrl: {!! isset($validatorUrl) ? '"' . $validatorUrl . '"' : 'null' !!},
            oauth2RedirectUrl: "{{ route('l5-swagger.'.$documentation.'.oauth2_callback', [], $useAbsolutePath) }}",

            requestInterceptor: function(request) {
                request.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
                return request;
            },

            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],

            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],

            layout: "StandaloneLayout",
            docExpansion : "{!! config('l5-swagger.defaults.ui.display.doc_expansion', 'none') !!}",
            deepLinking: true,
            filter: {!! config('l5-swagger.defaults.ui.display.filter') ? 'true' : 'false' !!},
            persistAuthorization: "{!! config('l5-swagger.defaults.ui.authorization.persist_authorization') ? 'true' : 'false' !!}",

        })

        window.ui = ui

        @if(in_array('oauth2', array_column(config('l5-swagger.defaults.securityDefinitions.securitySchemes'), 'type')))
        ui.initOAuth({
            usePkceWithAuthorizationCodeGrant: "{!! (bool)config('l5-swagger.defaults.ui.authorization.oauth2.use_pkce_with_authorization_code_grant') !!}"
        })
        @endif
    }
</script>
</body>
</html>
