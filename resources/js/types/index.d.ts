export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    is_admin: boolean;
    profile_photo_url: string;
    profile_photo_path?: string | null;
}

export interface Notification {
    success?: string;
    danger?: string;
    warning?: string;
    info?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    appName: string;
    logo?: string | null;
    notification?: Notification;
};

export interface iNotification {
    success?: string;
    danger?: string;
    warning?: string;
    info?: string;
}

export interface iLink {
    url: string;
    label: string;
    active: string;
}

export interface iOffering {
    id: number;
    offering_date: string;
    amount: number;
    type: { id: number; name: string } | null;
    user: { id: number; name: string } | null;
}

export interface iOfferings {
    data: iOffering[];
    current_page: number;
    first_page_url: string | null;
    from: number;
    last_page: number;
    last_page_url: string | null;
    links: Array<iLink>;
    next_page_url: string | null;
    path: string | null;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface iTithe {
    id: number;
    tithed_on: string;
    amount: number;
    user: { id: number; name: string } | null;
}

export interface iTithes {
    data: iTithe[];
    current_page: number;
    first_page_url: string | null;
    from: number;
    last_page: number;
    last_page_url: string | null;
    links: Array<iLink>;
    next_page_url: string | null;
    path: string | null;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface iAttendance {
    id: number;
    title: string;
    attendance_date: string;
    user: { id: number; name: string } | null;
    members?: iMember[];
}

export interface iAttendances {
    data: iAttendance[];
    current_page: number;
    first_page_url: string | null;
    from: number;
    last_page: number;
    last_page_url: string | null;
    links: Array<iLink>;
    next_page_url: string | null;
    path: string | null;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface iMember {
    id: number;
    first_name: string;
    last_name: string;
    photo: string | null;
    photo_url: string;
    phone: string;
    email: string | null;
    box_no?: string | null;
    post_code?: string | null;
    town?: string | null;
    address?: string | null;
    date_of_birth?: string | null;
    gender: string;
}

export interface iMembers {
    data: iMember[];
    current_page: number;
    first_page_url: string | null;
    from: number;
    last_page: number;
    last_page_url: string | null;
    links: Array<iLink>;
    next_page_url: string | null;
    path: string | null;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface iStat {
    name: string;
    value: number | string;
}

export interface iOfferingType {
    value: number;
    text: string;
}

export interface iRecipient {
    id: number;
    name: string;
    phone: string;
    status: string;
}

export interface iSms {
    id: number | null;
    message: string;
    sent_at: string;
    status: string;
    recipients: iRecipient[];
}

export interface iSmsMessages {
    data: iSms[];
    current_page: number;
    first_page_url: string | null;
    from: number;
    last_page: number;
    last_page_url: string | null;
    links: Array<iLink>;
    next_page_url: string | null;
    path: string | null;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface iContributionType {
    id: number;
    description: string;
    recurrent: number | boolean;
    recurrence_value: number;
    recurrence_unit: string;
    deadline?: string;
    amount: number;
    back_date: number | boolean;
    autoenroll: number | boolean;
    members?: number[];
}

export interface iContributionTypes {
    data: iContributionType[];
    current_page: number;
    first_page_url: string | null;
    from: number;
    last_page: number;
    last_page_url: string | null;
    links: Array<iLink>;
    next_page_url: string | null;
    path: string | null;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface iContributionMember {
    id: number;
    name: string;
    photo: string;
    phone: string;
    email: string | null;
    postal_address: string;
}

export interface iContributionMembers {
    data: iContributionMember[];
    current_page: number;
    first_page_url: string | null;
    from: number;
    last_page: number;
    last_page_url: string | null;
    links: Array<iLink>;
    next_page_url: string | null;
    path: string | null;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface iDepartment {
    id: number;
    title: string;
}

export interface iContribution {
    id: number | null;
    contribution_date: string;
    end_at: string;
    description: string;
    amount: number;
    paid: number;
    balance: number;
    status: string;
}

export interface iMemberWithContributions {
    id: number;
    name: string;
    photo: string;
    phone: string;
    email: string | null;
    postal_address: string;
    amount: number;
    paid: number;
    balance: number;
    contributions: iContribution[];
}

export interface iShowMembers {
    data: iMemberWithContributions[];
    current_page: number;
    first_page_url: string | null;
    from: number;
    last_page: number;
    last_page_url: string | null;
    links: Array<iLink>;
    next_page_url: string | null;
    path: string | null;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface LinkSubItem {
    name: string;
    caption: string;
}

export interface SidebarLink {
    name: string;
    caption: string;
    icon?: string;
    items?: LinkSubItem[] | null;
    show?: boolean;
    permission?: number | string | string[];
    as?: string;
    method?: string;
}


