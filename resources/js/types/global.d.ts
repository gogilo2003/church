import { AxiosInstance } from 'axios';
import { route as ziggyRoute } from 'ziggy-js';

declare global {
    interface Window {
        axios: AxiosInstance;
    }

    /* eslint-disable no-var */
    var route: typeof ziggyRoute;
}

declare module 'vue' {
    interface ComponentCustomProperties {
        route: typeof ziggyRoute;
        $page: any;
    }
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            auth: {
                user: {
                    id: number;
                    name: string;
                    email: string;
                    email_verified_at?: string;
                    is_admin: boolean;
                    profile_photo_url: string;
                    profile_photo_path?: string | null;
                };
            };
            appName: string;
            logo?: string | null;
            notification?: {
                success?: string;
                danger?: string;
                warning?: string;
                info?: string;
            };
        };
    }
}
