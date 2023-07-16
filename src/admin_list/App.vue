<script>
const wpData = window.wpData || {};
const wp_nonce = wpData.nonce;
const canEditPosts = wpData.canEditPosts;

export default {
  data: function () {
    return {
      staff_items: [],
      postCounts: {
        publish: 0,
        draft: 0,
        trash: 0,
      },
    };
  },
  methods: {
    async fetchStaffItems() {
      if (!canEditPosts) {
        console.log("User does not have edit_posts capability");
        return;
      }

      try {
        const statusParam = this.trashView ? "trash" : "any";
        const response = await fetch(
          `/wp-json/wp/v2/staff-members?status=${statusParam}`,
          {
            headers: {
              "X-WP-Nonce": wpData.nonce,
            },
          }
        );
        let posts = await response.json();

        // Fetch featured images
        for (let post of posts) {
          try {
            const postFeatured = post._links["wp:featuredmedia"][0].href;
            const featuredResponse = await fetch(postFeatured, {
              headers: {
                "X-WP-Nonce": wpData.nonce,
              },
            });
            const featuredImage = await featuredResponse.json();

            // Add the featured image to the post object
            post.featured =
              featuredImage.media_details.sizes.thumbnail.source_url;
          } catch (error) {
            console.warn(error + ". No featured image set.");
          }
        }
        this.staff_items = posts;
        console.log(posts);
        await this.fetchPostCounts();
      } catch (error) {
        console.error(error);
      }
    },
    async fetchPostCounts() {
      try {
        const response = await fetch("/wp-json/staff-hero/v1/counts/", {
          headers: {
            "X-WP-Nonce": wpData.nonce,
          },
        });

        if (!response.ok) {
          throw new Error("An error occurred while fetching the post counts.");
        }

        const counts = await response.json();
        this.postCounts = counts;
      } catch (error) {
        console.error(error);
      }
    },
    editStaffMember(id) {
      const editUrl = `/wp-admin/post.php?post=${id}&action=edit`;
      window.location.href = editUrl;
    },
    createNewStaffMember() {
      const newStaffMemberUrl = "/wp-admin/post-new.php?post_type=staff_member";
      window.location.href = newStaffMemberUrl;
    },
    async togglePublishStatus(item) {
      try {
        const newStatus =
          item.status === "publish" || item.status === "trash"
            ? "draft"
            : "publish";

        const response = await fetch(
          `/wp-json/staff-hero/v1/staff-members/${item.id}`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-WP-Nonce": wpData.nonce,
            },
            body: JSON.stringify({ status: newStatus }),
          }
        );

        if (!response.ok) {
          throw new Error("An error occurred while updating the post.");
        }

        await response.json();
        await this.fetchStaffItems();

        if (this.trashView && this.staff_items.length === 0) {
          this.toggleTrashView();
        }
      } catch (error) {
        console.error(error);
      }
    },
    async moveToTrash(item) {
      try {
        const response = await fetch(
          `/wp-json/staff-hero/v1/staff-members/${item.id}`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-WP-Nonce": wpData.nonce,
            },
            body: JSON.stringify({ status: "trash" }),
          }
        );

        if (!response.ok) {
          throw new Error("An error occurred while trashing the post.");
        }

        await response.json();
        await this.fetchStaffItems();
      } catch (error) {
        console.error(error);
      }
    },
    toggleTrashView() {
      this.trashView = !this.trashView;
      this.fetchStaffItems();
    },
  },
  created: function () {
    this.fetchStaffItems();
    this.fetchPostCounts();
  },
};
</script>

<template>
  <p>Published: {{ postCounts.publish }}</p>
  <p>Draft: {{ postCounts.draft }}</p>
  <p>Trash: {{ postCounts.trash }}</p>
  <button class="button" @click="createNewStaffMember">New Staff Member</button>
  <button v-if="postCounts.trash > 0" class="button" @click="toggleTrashView">
    {{ trashView ? "View All Items" : "View Trashed Items" }}
  </button>
  <ul class="staff-list">
    <li :class="item.status" v-for="item in staff_items" :key="item.id">
      <div class="list-image">
        <img
          v-if="item.featured"
          :src="item.featured"
          :alt="item.title.rendered"
        />
        <div v-else class="list-noimage"></div>
      </div>
      <div class="list-info">
        <h3>{{ item.title.rendered }}</h3>
        <div>
          slug: {{ item.slug }} created: {{ item.date }} modified:
          {{ item.modified }} status: {{ item.status }}
        </div>
      </div>
      <div class="list-actions">
        <a class="button" :href="item.link">{{
          item.status === "publish" ? "View" : "Preview"
        }}</a>
        <button class="button" v-if="!trashView" @click="moveToTrash(item)">
          Trash
        </button>
        <button class="button" @click="togglePublishStatus(item)">
          {{
            item.status === "publish"
              ? "Unpublish"
              : item.status === "trash"
              ? "Restore"
              : "Publish"
          }}
        </button>
        <button class="button" @click="editStaffMember(item.id)">Edit</button>
      </div>
    </li>
  </ul>
  <p v-if="staff_items.length === 0">
    {{ trashView ? "No trashed items found." : "No items found." }}
  </p>
</template>

<style scoped>
.staff-list {
  margin-right: 20px;
}
.staff-list h3 {
  padding: 0;
  margin: 0;
}

.staff-list li {
  display: grid;
  grid-template-columns: 100px 1fr 300px;
  padding: 10px 20px;
  border-bottom: solid 1px #a7a7a7;
  margin: 0;
}

.staff-list {
  background: #fff;
  border: solid 1px #a7a7a7;
  border-radius: 5px;
}

.staff-list li:last-child {
  border-bottom: 0 none;
}

.staff-list li.draft > * {
  opacity: 0.45;
}
.staff-list li.draft .list-actions {
  opacity: 1;
}

.staff-list .list-image img {
  border-radius: 70px;
  width: 70px;
}

.staff-list .list-image .list-noimage {
  height: 70px;
  width: 70px;
  background: #fafafa;
  border-radius: 70px;
}

.staff-list .list-actions {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr 1fr;
  align-items: center;
  text-align: center;
}
</style>
