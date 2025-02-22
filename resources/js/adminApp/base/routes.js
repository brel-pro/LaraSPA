import Index        from './components/Index.vue'
import NotFound     from './components/NotFound.vue'
import Base         from './components/Base.vue'
import Child        from './components/Child.vue'
import Login        from '../modules/auth/components/Login.vue'
var indexOrLoginPage = Index

const autoImportModules = import.meta.glob('../modules/*/routes.js', { import: 'routes' })
const indexOrLogin = import.meta.env.VITE_ADMIN_PANEL_INDEXLOGIN
let moduleRoutes = []

for (const path in autoImportModules) {
    const routes = await autoImportModules[path]()
    moduleRoutes = moduleRoutes.concat(routes)
}

if (indexOrLogin == 1) {    
    var indexOrLoginPage = Login
}

export const routes = [
    {
        path: '/',
        component: Base,
        children: [
            {
                path: 'dashboard',
                name: 'Home',
                component: Child,
                meta: {auth: true},
                children: [
                    ...moduleRoutes,
                ]
            },
            {
                path: '/',
                component: indexOrLoginPage,
                name: 'index',
                meta: {layout: 'Welcome'},
                hidden: true,
            },
            {
                path: ':pathMatch(.*)*',
                component: NotFound,
                name: '404',
                meta: {
                    layout: 'Welcome',
                    auth: undefined
                },
            }
        ]
    }
]

