# monolog:
#     channels: ["security", "request", "process"]
#     handlers:
#         process_events:
#             type: stream
#             path: "%kernel.logs_dir%/process_events.log"
#             level: info
#             channels: ["process"]
#             formatter: "app.formatter.json"
# 
#         security_events:
#             type: stream
#             path: "%kernel.logs_dir%/security.log"
#             level: info
#             channels: ["security"]
#
# services:
#     app.formatter.json:
#         class: Monolog\Formatter\JsonFormatter

# config/packages/monolog.yaml (when ready to integrate):
# Aktiviere für strukturiertes Logging zu ELK/Splunk:

framework:
    monolog:
        channels:
            - deprecation
            - php

