import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { centralAdminLinks, linksBottom, tenantLinks } from '@/links';
import type { SidebarLink } from '@/types';

type LinkPermission = number | string | string[] | undefined;

interface AuthUserProps {
    is_admin?: boolean;
    permissions?: string[];
}

interface PageProps {
    auth?: {
        user?: AuthUserProps | null;
    };

    [key: string]: unknown;
}

const normalizePermission = (permission: LinkPermission): string[] => {
    if (permission === undefined || permission === null) {
        return [];
    }

    if (Array.isArray(permission)) {
        return permission.flatMap(normalizePermission);
    }

    if (permission === 0) {
        return [];
    }

    if (permission === 1) {
        return ['admin'];
    }

    return [String(permission)];
};

export function useLinks() {
    const page = usePage<PageProps>();

    const authUser = computed(() => page.props.auth?.user ?? null);

    const isAdmin = computed(() => authUser.value?.is_admin ?? false);

    const canAccess = (link: SidebarLink): boolean => {
        const user = authUser.value;

        if (!user) {
            return false;
        }

        const permissions = user.permissions ?? [];
        const required = normalizePermission(link.permission);

        if (required.length === 0) {
            return true;
        }

        if (required.includes('admin')) {
            return user.is_admin === true;
        }

        return user.is_admin
            || permissions.includes('*')
            || required.some((permission) => permissions.includes(permission));
    };

    const isLinkActive = (link: SidebarLink, exact = false): boolean => {
        const current = route().current();

        if (!current) {
            return false;
        }

        return route().current(link.name) || (!exact && current.startsWith(link.name));
    };

    const isCentralContext = computed(() => route().current()?.startsWith('central.') ?? false);

    const filterLinks = (links: SidebarLink[]): SidebarLink[] =>
        links.filter((link) => link.show !== false && canAccess(link));

    const topLinks = computed<SidebarLink[]>(() =>
        isCentralContext.value ? filterLinks(centralAdminLinks.value) : filterLinks(tenantLinks.value)
    );

    const bottomLinks = computed<SidebarLink[]>(() => filterLinks(linksBottom.value));

    return {
        topLinks,
        bottomLinks,
        isCentralContext,
        isLinkActive,
    };
}