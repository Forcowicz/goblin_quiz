export * from "./auth";

export interface AIResponseDTO {
    messages: string[];
    conversationId?: string;
    reaction?: 'angry' | 'laughing' | null;
}
