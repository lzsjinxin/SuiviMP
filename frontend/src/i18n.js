import { createI18n } from 'vue-i18n';

function loadLocaleMessages () {
  const locales = require.context('./assets/json/locales', true, /[A-Za-z0-9-_,\s]+\.json$/i)
  const messages = {}
  locales.keys().forEach(key => {
    const matched = key.match(/([A-Za-z0-9-_]+)\./i)
    if (matched && matched.length > 1) {
      const locale = matched[1]
      messages[locale] = locales(key)
    }
  })
  return messages
}

export default createI18n({
  locale: 'fr',
  fallbackLocale: 'en',
  messages: loadLocaleMessages(),
});
