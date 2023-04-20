import { createRouter, createWebHistory } from 'vue-router'
import {routes} from '../base/routes'

var subURL = ''


// TODO make this beauty
if (import.meta.env.VITE_SITE_SUB_URL ?? import.meta.env.VITE_SITE_SUB_URL != '') {
    subURL = import.meta.env.VITE_SITE_SUB_URL    
}

if (import.meta.env.VITE_ADMIN_PANEL_PREFIX && import.meta.env.VITE_ADMIN_PANEL_PREFIX != '') {
    subURL += import.meta.env.VITE_ADMIN_PANEL_PREFIX   
}

const router = createRouter({
    history: createWebHistory(subURL),
    routes,
    scrollBehavior(to, from, savedPosition) {
        return new Promise((resolve) => {
            if (to.hash) {
                resolve({ selector: to.hash })
            } else if (savedPosition) {
                resolve(savedPosition)
            } else {
                resolve({x: 0, y: 0})
            }
        })
    }
})

export default (app) => {
    app.router = router

    app.use(router)
}

