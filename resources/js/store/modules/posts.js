export const posts = {
    state: () => ({
        posts: [],
        replyId: null
    }),
    mutations: {
        setReplyId(state, replyId) {
            state.replyId = replyId;
        },
        setPosts(state, posts) {
            state.posts = posts;
        },
        addPost(state, post) {
            state.posts.unshift(post);
        },
        addPostToParent(state, { parentId, newPost }) {
            const addPostTo = (posts) => {
                for (const post of posts) {
                    if (post.id === parentId) {
                        if (!post.posts) {
                            post.posts = [];
                        }
                        post.posts.push(newPost);
                        state.replyId = null
                        return;
                    }
                    if (post.posts) {
                        addPostTo(post.posts);
                    }
                }
            };
            addPostTo(state.posts);
        }
    },
    getters: {
        getPosts(state) {
            return state.posts
        },
        getReplyId(state) {
            return state.replyId
        }
    },
}