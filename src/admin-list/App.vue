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
        console.log('User does not have edit_posts capability');
        return;
      }

      try {
        const statusParam = this.trashView ? "trash" : "any";
        const response = await fetch(`/wp-json/wp/v2/staff-members?status=${statusParam}`, {
          headers: {
            "X-WP-Nonce": wpData.nonce,
          },
        });
        const posts = await response.json();
        this.staff_items = posts;

        await this.fetchPostCounts(); // Fetch the post counts
      } catch (error) {
        console.error(error);
      }
    },
    async fetchPostCounts() {
      try {
        const response = await fetch('/wp-json/staff-members/v1/counts/', {
          headers: {
            'X-WP-Nonce': wpData.nonce,
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
      const newStaffMemberUrl = '/wp-admin/post-new.php?post_type=staff_member';
      window.location.href = newStaffMemberUrl;
    },
    async togglePublishStatus(item) {
      try {
        const newStatus = item.status === "publish" || item.status === "trash" ? "draft" : "publish";

        const response = await fetch(`/wp-json/staff-members/v1/staff-members/${item.id}`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-WP-Nonce": wpData.nonce,
          },
          body: JSON.stringify({ status: newStatus }),
        });

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
        const response = await fetch(`/wp-json/staff-members/v1/staff-members/${item.id}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': wpData.nonce,
          },
          body: JSON.stringify({ status: 'trash' }),
        });

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
    }
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
  <button @click="createNewStaffMember">New Staff Member</button>
  <button @click="toggleTrashView">
    {{ trashView ? "View All Items" : "View Trashed Items" }}
  </button>
  <ul class="staff-list">
    <li v-for="item in staff_items" :key="item.id">
      <div class="list-info">
        <h3>{{ item.title.rendered }}</h3>
        <div>
          slug: {{ item.slug }}
          created: {{ item.date }}
          modified: {{ item.modified }}
          status: {{ item.status }}
        </div>
      </div>
      <div class="list-actions">
        <button v-if="!trashView" @click="moveToTrash(item)">Trash</button>
        <button @click="togglePublishStatus(item)">
          {{
            item.status === "publish"
            ? "Unpublish"
            : item.status === "trash"
              ? "Restore"
              : "Publish"
          }}
        </button>
        <button @click="editStaffMember(item.id)">Edit</button>
      </div>
    </li>
  </ul>
  <p v-if="staff_items.length === 0">
    {{ trashView ? "No trashed items found." : "No items found." }}
  </p>
</template>

<style scoped>
.staff-list h3 {
  padding: 0;
  margin: 0;
}

.staff-list li {
  display: flex;
  justify-content: space-between;
  padding: 10px 20px;
  border-bottom: solid 1px;
  margin: 0;
}

.staff-list {
  background: #fff;
  border: solid 1px;
}

.staff-list li:last-child {
  border-bottom: 0 none;
}
</style>