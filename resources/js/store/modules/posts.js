export const posts = {
    state: () => ({
        posts: [],
        replyId: null,
        changeId: null
    }),
    mutations: {
        setReplyId(state, replyId) {
            state.replyId = replyId;
        },
        setPosts(state, posts) {
            state.posts = posts;
        },
        setChangeId(state, changeId) {
            state.changeId = changeId;
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
                        if(!post.posts.find(el => el.id == newPost.id)){
                            post.posts.push(newPost);
                            state.replyId = null
                        }
                        return;
                    }
                    if (post.posts) {
                        addPostTo(post.posts);
                    }
                }
            };
            addPostTo(state.posts);
        },
        updatePost(state, post) {
            for (const key of Object.keys(state.posts)) {
                const value = state.posts[key];
                if(value.id == post.id) {
                    console.log(1123);
                    state.posts[key].change = post.change
                    return
                }
            }
        }
        
    },
    getters: {
        getPosts(state) {
            return state.posts
        },
        getReplyId(state) {
            return state.replyId
        },
        getChangeId(state) {
            return state.changeId
        }
    },
}