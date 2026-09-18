/**
 * Listens on the signed in user's PRIVATE Reverb channel and hands every alert
 * to the Livewire component, which renders it as a Filament notification.
 */
(function () {
    let started = false

    const offline = function (isOffline) {
        document.querySelectorAll('[data-reverb-offline]').forEach(function (element) {
            element.hidden = ! isOffline
        })
    }

    const loadEcho = function (cdn) {
        if (window.Echo && typeof window.Echo.private === 'function') {
            return Promise.resolve(null)
        }

        if (typeof window.Echo === 'function') {
            return Promise.resolve(window.Echo)
        }

        return new Promise(function (resolve, reject) {
            const script = document.createElement('script')
            script.src = cdn
            script.onload = function () {
                resolve(window.Echo)
            }
            script.onerror = reject
            document.head.appendChild(script)
        })
    }

    const connect = function (settings) {
        return loadEcho(settings.echoCdn).then(function (EchoConstructor) {
            // The host application already bundles and configures Echo: reuse that instance.
            if (EchoConstructor === null) {
                return window.Echo
            }

            window.Echo = new EchoConstructor({
                broadcaster: 'reverb',
                key: settings.key,
                wsHost: settings.host,
                wsPort: settings.port,
                wssPort: settings.port,
                forceTLS: settings.scheme === 'https',
                enabledTransports: ['ws', 'wss'],
            })

            return window.Echo
        })
    }

    const listen = function () {
        const root = document.getElementById('filament-reverb-driver')

        if (! root || ! root.dataset.reverb || started) {
            return
        }

        const settings = JSON.parse(root.dataset.reverb)

        if (! settings.key || ! settings.channel) {
            return
        }

        started = true

        connect(settings)
            .then(function (echo) {
                const connector = echo.connector && echo.connector.pusher

                if (connector) {
                    connector.connection.bind('connected', function () {
                        offline(false)
                    })
                    connector.connection.bind('unavailable', function () {
                        offline(true)
                    })
                    connector.connection.bind('disconnected', function () {
                        offline(true)
                    })
                }

                echo.private(settings.channel).listen(
                    '.filament-alerts.notification',
                    function (payload) {
                        window.Livewire.dispatch('reverb-notification', { data: payload })
                    },
                )
            })
            .catch(function () {
                started = false
                offline(true)
            })
    }

    document.addEventListener('DOMContentLoaded', listen)
    document.addEventListener('livewire:navigated', listen)

    if (document.readyState !== 'loading') {
        listen()
    }
})()
