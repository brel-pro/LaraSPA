import * as dayjs from 'dayjs'
import 'dayjs/locale/en'
import 'dayjs/locale/ru'
import LocalizedFormat from 'dayjs/plugin/localizedFormat'

export const changeDayjsLocale = function (locale) {
    dayjs.locale(locale)
    dayjs.extend(LocalizedFormat)
}

export default dayjs
