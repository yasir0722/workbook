import type { Page, PagePayload, PaginatedPosts } from './types';

interface ApiCollection<T> {
    data: T[];
}

interface ApiItem<T> {
    data: T;
}

async function request<T>(path: string, init?: RequestInit): Promise<T> {
    const response = await fetch(`/api/${path}`, {
        headers: {
            Accept: 'application/json',
            ...(init?.body ? { 'Content-Type': 'application/json' } : {}),
            ...init?.headers,
        },
        ...init,
    });

    if (!response.ok) {
        const payload = await response.json().catch(() => null);
        throw new Error(payload?.message ?? 'The request could not be completed.');
    }

    return response.status === 204 ? (undefined as T) : response.json();
}

export const api = {
    feed: async (): Promise<Page[]> => (await request<ApiCollection<Page>>('feed?per_page=4')).data,
    pages: async (): Promise<Page[]> => (await request<ApiCollection<Page>>('pages')).data,
    createPage: async (payload: PagePayload): Promise<Page> =>
        (await request<ApiItem<Page>>('pages', { method: 'POST', body: JSON.stringify(payload) })).data,
    updatePage: async (pageId: number, payload: PagePayload): Promise<Page> =>
        (await request<ApiItem<Page>>(`pages/${pageId}`, { method: 'PUT', body: JSON.stringify(payload) })).data,
    deletePage: async (pageId: number): Promise<void> => request<void>(`pages/${pageId}`, { method: 'DELETE' }),
    posts: async (pageId: number, page: number): Promise<PaginatedPosts> =>
        request<PaginatedPosts>(`pages/${pageId}/posts?per_page=4&page=${page}`),
};
