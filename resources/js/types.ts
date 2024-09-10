export interface iNotification {
    success?: string | null
    danger?: string | null
    warning?: string | null
    info?: string | null
}
export interface iLink {
    url: string,
    label: string,
    active: string,
}
export interface iOffering {
    id: number
    offering_date: string
    amount: number
    type: number | { id: number, name: string } | null
    user: { id: number, name: string } | null
}
export interface iOfferings {
    data: iOffering[]
    current_page: number
    first_page_url: string | null
    from: number
    last_page: number
    last_page_url: string | null
    links: Array<iLink>
    next_page_url: string | null
    path: string | null
    per_page: number
    prev_page_url: string | null
    to: number
    total: number
}
export interface iTithe {
    id: number
    tithed_on: string
    amount: number
    user: { id: number, name: string } | null
}
export interface iTithes {
    data: iTithe[]
    current_page: number
    first_page_url: string | null
    from: number
    last_page: number
    last_page_url: string | null
    links: Array<iLink>
    next_page_url: string | null
    path: string | null
    per_page: number
    prev_page_url: string | null
    to: number
    total: number
}
export interface iAttendance {
    id: number
    title: string
    attendance_date: string
    user: { id: number, name: string } | null
}
export interface iAttendances {
    data: iAttendance[]
    current_page: number
    first_page_url: string | null
    from: number
    last_page: number
    last_page_url: string | null
    links: Array<iLink>
    next_page_url: string | null
    path: string | null
    per_page: number
    prev_page_url: string | null
    to: number
    total: number
}
export interface iMember {
    id: number
    name: string
    photo: string
    phone: string
    email: string
    postal_address: string
}
export interface iMembers {
    data: iMember[]
    current_page: number
    first_page_url: string | null
    from: number
    last_page: number
    last_page_url: string | null
    links: Array<iLink>
    next_page_url: string | null
    path: string | null
    per_page: number
    prev_page_url: string | null
    to: number
    total: number
}

export interface iStat {
    name: string
    value: number
}

export interface iOfferingType {
    id: number
    name: string
}

export interface iRecipient {
    id: number
    name: string
    phone: string
    status: string
}
export interface iSms {
    id: number | null
    message: string
    sent_at: string
    status: string
    recipients: iRecipient[]
}

export interface iSmsMessages {
    data: iSms[]
    current_page: number
    first_page_url: string | null
    from: number
    last_page: number
    last_page_url: string | null
    links: Array<iLink>
    next_page_url: string | null
    path: string | null
    per_page: number
    prev_page_url: string | null
    to: number
    total: number
}
