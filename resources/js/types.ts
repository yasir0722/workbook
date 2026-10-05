export interface Media {
    id: number;
    media_type: string;
    media_url: string;
    thumbnail_url: string | null;
    sort_order: number;
}

export interface Post {
    id: number;
    facebook_post_id: string | null;
    message: string | null;
    permalink: string | null;
    published_at: string | null;
    media: Media[];
}

export interface Page {
    id: number;
    facebook_page_id: string | null;
    name: string;
    username: string | null;
    url: string | null;
    category: string | null;
    enabled: boolean;
    sort_order: number;
    last_fetched_at: string | null;
    posts?: Post[];
}

export interface PaginatedPosts {
    data: Post[];
    meta: {
        current_page: number;
        last_page: number;
    };
}

export interface PagePayload {
    name: string;
    username: string | null;
    url: string | null;
    category: string | null;
    enabled: boolean;
    sort_order?: number;
}
