<template>
  <div class="admin-users">
    <div class="bg-white shadow rounded-lg overflow-hidden">
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-medium text-gray-900">Kelola Pengguna</h2>
          <div class="flex items-center space-x-4">
            <!-- Filter Role -->
            <select 
              v-model="filters.role" 
              @change="fetchUsers"
              class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Semua Role</option>
              <option value="admin">Admin</option>
              <option value="editor">Editor</option>
              <option value="user">User</option>
            </select>

            <!-- Search Input -->
            <div class="relative">
              <input
                type="text"
                v-model="filters.search"
                @input="debouncedSearch"
                placeholder="Cari pengguna..."
                class="border border-gray-300 rounded-md pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
              >
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>

            <!-- Add User Button -->
            <button
              @click="openAddModal"
              class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Tambah User
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="px-6 py-8">
        <div class="flex justify-center items-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
          <span class="ml-3 text-gray-500">Memuat pengguna...</span>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="px-6 py-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          <p class="font-medium">Error:</p>
          <p>{{ error }}</p>
          <button 
            @click="fetchUsers" 
            class="mt-2 bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700"
          >
            Coba Lagi
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!users || users.data.length === 0" class="px-6 py-8">
        <div class="text-center text-gray-500">
          <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
          </svg>
          <p class="text-lg font-medium">Tidak ada pengguna</p>
          <p class="mt-1">Tidak ada pengguna yang sesuai dengan filter Anda.</p>
          <button
            @click="showAddModal = true"
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
          >
            Tambah Pengguna Pertama
          </button>
        </div>
      </div>

      <!-- Users Table -->
      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bergabung</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
              <!-- User Info -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                      <span class="text-sm font-medium text-white">
                        {{ user.name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">
                      {{ user.name }}
                    </div>
                    <div class="text-sm text-gray-500">
                      ID: {{ user.id }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Email -->
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ user.email }}</div>
                <div class="text-sm text-gray-500">
                  {{ user.email_verified_at ? 'Terverifikasi' : 'Belum verifikasi' }}
                </div>
              </td>

              <!-- Role -->
              <td class="px-6 py-4 whitespace-nowrap">
                <select
                  v-model="user.role"
                  @change="updateUserRole(user.id, user.role)"
                  class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :class="getRoleClasses(user.role)"
                >
                  <option value="user">User</option>
                  <option value="editor">Editor</option>
                  <option value="admin">Admin</option>
                </select>
              </td>

              <!-- Join Date -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(user.created_at) }}
              </td>

              <!-- Status -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClasses(user)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getStatusText(user) }}
                </span>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex space-x-2">
                  <!-- Edit Button -->
                  <button 
                    @click="editUser(user)"
                    class="text-blue-600 hover:text-blue-900 flex items-center"
                    title="Edit User"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit
                  </button>

                  <!-- Delete Button -->
                  <button 
                    @click="deleteUser(user)"
                    class="text-red-600 hover:text-red-900 flex items-center"
                    title="Hapus User"
                    :disabled="user.id === currentUser?.id"
                    :class="{ 'opacity-50 cursor-not-allowed': user.id === currentUser?.id }"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="users && users.meta && users.meta.last_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <Pagination 
          :current-page="users.meta.current_page" 
          :last-page="users.meta.last_page"
          :total="users.meta.total"
          @page-changed="handlePageChange"
        />
      </div>
    </div>

    <!-- Add/Edit User Modal -->
    <Modal
      v-model="showModal"
      :title="isEditing ? 'Edit Pengguna' : 'Tambah Pengguna'"
      :max-width="'md'"
      @close="resetForm"
    >
      <template #content>
        <form @submit.prevent="submitForm">
          <!-- Name -->
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
            <input
              type="text"
              id="name"
              v-model="form.name"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.name }"
              placeholder="Masukkan nama lengkap"
            />
            <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name[0] }}</p>
          </div>

          <!-- Email -->
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
            <input
              type="email"
              id="email"
              v-model="form.email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.email }"
              placeholder="Masukkan alamat email"
            />
            <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</p>
          </div>

          <!-- Role -->
          <div class="mb-4">
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
            <select
              id="role"
              v-model="form.role"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.role }"
            >
              <option value="">Pilih Role</option>
              <option value="user">User</option>
              <option value="editor">Editor</option>
              <option value="admin">Admin</option>
            </select>
            <p v-if="errors.role" class="text-red-500 text-xs mt-1">{{ errors.role[0] }}</p>
          </div>

          <!-- Password (hanya untuk tambah user) -->
          <div v-if="!isEditing" class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
            <input
              type="password"
              id="password"
              v-model="form.password"
              required
              :minlength="8"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.password }"
              placeholder="Minimal 8 karakter"
            />
            <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password[0] }}</p>
          </div>

          <!-- Password Confirmation (hanya untuk tambah user) -->
          <div v-if="!isEditing" class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password *</label>
            <input
              type="password"
              id="password_confirmation"
              v-model="form.password_confirmation"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :class="{ 'border-red-500': errors.password_confirmation }"
              placeholder="Ulangi password"
            />
            <p v-if="errors.password_confirmation" class="text-red-500 text-xs mt-1">{{ errors.password_confirmation[0] }}</p>
          </div>

          <!-- Form Actions -->
          <div class="flex items-center justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="showModal = false"
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="loadingForm"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
            >
              <span v-if="loadingForm">Menyimpan...</span>
              <span v-else>{{ isEditing ? 'Update' : 'Simpan' }}</span>
            </button>
          </div>
        </form>
      </template>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal
      v-model="showDeleteModal"
      title="Hapus Pengguna"
      max-width="sm"
    >
      <template #content>
        <div class="text-center">
          <svg class="w-16 h-16 text-red-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
          </svg>
          
          <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Penghapusan</h3>
          <p class="text-gray-600 mb-4">
            Apakah Anda yakin ingin menghapus pengguna <strong>{{ userToDelete?.name }}</strong>?
            Tindakan ini tidak dapat dibatalkan.
          </p>

          <div class="flex justify-center space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
            >
              Batal
            </button>
            <button
              @click="confirmDelete"
              :disabled="loadingDelete"
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50"
            >
              <span v-if="loadingDelete">Menghapus...</span>
              <span v-else>Hapus</span>
            </button>
          </div>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script>
import { ref, onMounted, watch, computed, nextTick } from 'vue'
import { useApi } from '../../../composables/useApi'
import { useAuthStore } from '../../../stores/auth'
import { useRoute, useRouter } from 'vue-router'
import Pagination from '../../Shared/Pagination.vue'
import Modal from '../../Shared/Modal.vue'

export default {
  name: 'AdminUserList',
  components: {
    Pagination,
    Modal
  },
  setup() {
    const users = ref(null)
    const loading = ref(true)
    const error = ref('')
    const route = useRoute()
    const router = useRouter()
    
    // State untuk modal - pastikan initialized sebagai false
    const showModal = ref(false)
    const showDeleteModal = ref(false)
    const loadingForm = ref(false)
    const loadingDelete = ref(false)
    const isEditing = ref(false)
    const userToDelete = ref(null)
    
    const filters = ref({
      role: '',
      search: '',
      page: 1,
      per_page: 15
    })

    const form = ref({
      id: null,
      name: '',
      email: '',
      role: '',
      password: '',
      password_confirmation: ''
    })

    const errors = ref({})
    const authStore = useAuthStore()
    const { makeRequest } = useApi()

    const currentUser = computed(() => authStore.user)

    // Debug: Log state modal
    watch([showModal, showDeleteModal], ([modal, deleteModal]) => {
      
    })

    // Function untuk reset form dan close modal
    const resetFormAndCloseModal = () => {
      form.value = {
        id: null,
        name: '',
        email: '',
        role: '',
        password: '',
        password_confirmation: ''
      }
      errors.value = {}
      isEditing.value = false
      showModal.value = false
      showDeleteModal.value = false
      userToDelete.value = null
    }

    // Function untuk open modal tambah user
    const openAddModal = () => {
      resetFormAndCloseModal()
      isEditing.value = false
      // Gunakan nextTick untuk memastikan DOM sudah update
      nextTick(() => {
        showModal.value = true
      })
    }

    const getRoleClasses = (role) => {
      const classes = {
        admin: 'bg-purple-100 text-purple-800',
        editor: 'bg-blue-100 text-blue-800',
        user: 'bg-gray-100 text-gray-800'
      }
      return classes[role] || 'bg-gray-100 text-gray-800'
    }

     const getStatusClasses = (user) => {
      if (!user.email_verified_at) {
        return 'bg-yellow-100 text-yellow-800'
      }
      return 'bg-green-100 text-green-800'
    }

    const getStatusText = (user) => {
      if (!user.email_verified_at) {
        return 'Belum Verifikasi'
      }
      return 'Aktif'
    }

     const formatDate = (dateString) => {
      try {
        return new Date(dateString).toLocaleDateString('id-ID', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        })
      } catch {
        return dateString
      }
    }

    // Function untuk open modal edit user
    const editUser = (user) => {
      resetFormAndCloseModal()
      isEditing.value = true
      form.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        password: '',
        password_confirmation: ''
      }
      nextTick(() => {
        showModal.value = true
      })
    }

    const updateUserRole = async (userId, newRole) => {
      try {
        const response = await makeRequest(`/api/admin/users/${userId}/role`, 'PUT', { role: newRole })
        
        if (response.success) {
          // Update local state
          if (users.value && users.value.data) {
            const userIndex = users.value.data.findIndex(u => u.id === userId)
            if (userIndex !== -1) {
              users.value.data[userIndex].role = newRole
            }
          }
          
          alert('Role pengguna berhasil diperbarui')
        }
        
      } catch (err) {
        console.error('Failed to update user role:', err)
        alert('Gagal memperbarui role pengguna: ' + (err.response?.data?.message || err.message))
        // Reload to get correct data
        fetchUsers()
      }
    }

    // Function untuk open modal delete
    const deleteUser = (user) => {
      if (user.id === currentUser.value?.id) {
        alert('Anda tidak dapat menghapus akun sendiri')
        return
      }
      
      resetFormAndCloseModal()
      userToDelete.value = user
      nextTick(() => {
        showDeleteModal.value = true
      })
    }

    // Function untuk confirm delete
    const confirmDelete = async () => {
      if (!userToDelete.value) return

      try {
        loadingDelete.value = true
        const response = await makeRequest(`/api/admin/users/${userToDelete.value.id}`, 'DELETE')
        
        if (response.success) {
          // Remove from local state
          if (users.value && users.value.data) {
            users.value.data = users.value.data.filter(u => u.id !== userToDelete.value.id)
          }
          
          alert('Pengguna berhasil dihapus')
          resetFormAndCloseModal()
          fetchUsers()
        }
        
      } catch (err) {
        console.error('Failed to delete user:', err)
        alert('Gagal menghapus pengguna: ' + (err.response?.data?.message || err.message))
      } finally {
        loadingDelete.value = false
      }
    }

    // Function untuk submit form
    const submitForm = async () => {
      try {
        loadingForm.value = true
        errors.value = {}

        let url, method
        if (isEditing.value) {
          url = `/api/admin/users/${form.value.id}`
          method = 'PUT'
        } else {
          url = '/api/admin/users'
          method = 'POST'
        }

        const data = { ...form.value }
        if (isEditing.value) {
          delete data.password
          delete data.password_confirmation
        }

        const response = await makeRequest(url, method, data)
        
        if (response.success) {
          alert(isEditing.value ? 'Pengguna berhasil diperbarui' : 'Pengguna berhasil ditambahkan')
          resetFormAndCloseModal()
          fetchUsers()
        }
        
      } catch (err) {
        console.error('Form submission error:', err)
        if (err.response?.status === 422) {
          errors.value = err.response.data.errors || {}
        } else {
          alert('Terjadi kesalahan: ' + (err.response?.data?.message || err.message))
        }
      } finally {
        loadingForm.value = false
      }
    }

    // Fetch users
    const fetchUsers = async () => {
      try {
        loading.value = true
        error.value = ''
        
        const params = {
          page: filters.value.page,
          per_page: filters.value.per_page,
          role: filters.value.role,
          search: filters.value.search
        }

        const response = await makeRequest('/api/admin/users', 'GET', null, { params })
        
        
        if (response.success) {
          users.value = {
            data: response.data,
            meta: response.meta
          }
        } else {
          throw new Error(response.message || 'Failed to fetch users')
        }
        
      } catch (err) {
        console.error('Failed to fetch users:', err)
        error.value = err.message || 'Gagal memuat pengguna'
      } finally {
        loading.value = false
      }
    }

    // Pastikan modal tertutup saat component mounted
    onMounted(() => {
      
      resetFormAndCloseModal()
      fetchUsers()
      
      // Tambahkan event listener untuk escape key
      const handleEscape = (event) => {
        if (event.key === 'Escape') {
          resetFormAndCloseModal()
        }
      }
      
      window.addEventListener('keydown', handleEscape)
      
      // Cleanup
      return () => {
        window.removeEventListener('keydown', handleEscape)
      }
    })

    return {
      users,
      loading,
      error,
      filters,
      form,
      errors,
      showModal,
      showDeleteModal,
      loadingForm,
      loadingDelete,
      isEditing,
      userToDelete,
      currentUser,
      getRoleClasses,
      getStatusClasses,
      getStatusText,
      formatDate,
      openAddModal,
      editUser,
      updateUserRole,
      deleteUser,
      confirmDelete,
      submitForm,
      resetFormAndCloseModal,
      fetchUsers
    }
  }
}
</script>

<style scoped>
.admin-users {
  min-height: 600px;
}

tr:hover {
  background-color: #f9fafb;
}
</style>