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
export interface iMember {
    id: Number,
    name: String,
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
