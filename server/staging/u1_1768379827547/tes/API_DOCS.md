# Notezque REST API Documentation

Basis URL (default saat `php artisan serve`):

- `http://127.0.0.1:8000`
- Semua endpoint API berada di bawah prefix `/api`.

---

## 1. Autentikasi (Sanctum Token)

### 1.1 Register

- **Method**: POST
- **URL**: `/api/auth/register`
- **Body (JSON)**:
  ```json
  {
    "name": "Ali",
    "email": "ali@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }
  ```
- **Response (200/201)**:
  - `token`: string (dipakai sebagai Bearer Token)
  - `token_type`: "Bearer"
  - `user`: data user

### 1.2 Login

- **Method**: POST
- **URL**: `/api/auth/login`
- **Body (JSON)**:
  ```json
  {
    "email": "ali@example.com",
    "password": "password123",
    "device_name": "postman"
  }
  ```
- **Response**: sama seperti register (ada `token`).

### 1.3 Profil dari Token (Me)

- **Method**: GET
- **URL**: `/api/auth/me`
- **Header**:
  - `Authorization: Bearer <TOKEN>`

### 1.4 Logout (hapus token aktif)

- **Method**: POST
- **URL**: `/api/auth/logout`
- **Header**:
  - `Authorization: Bearer <TOKEN>`

### 1.5 Logout All (hapus semua token user)

- **Method**: POST
- **URL**: `/api/auth/logout-all`
- **Header**:
  - `Authorization: Bearer <TOKEN>`

---

## 2. Tugas (Tasks)

Semua endpoint berikut **wajib** header:
- `Authorization: Bearer <TOKEN>`

Model: `App\Models\Task`

Field penting:
- `title` (string)
- `description` (string)
- `due_date` (date, opsional)
- `priority` (low|medium|high, default: medium)
- `status` (pending|completed, default: pending)

### 2.1 List Tugas

- **Method**: GET
- **URL**: `/api/tasks`

### 2.2 Detail Tugas

- **Method**: GET
- **URL**: `/api/tasks/{id}`

### 2.3 Buat Tugas

- **Method**: POST
- **URL**: `/api/tasks`
- **Body (JSON)**:
  ```json
  {
    "title": "Tugas PBO",
    "description": "Kumpulkan minggu depan",
    "due_date": "2025-12-20",
    "priority": "high",
    "status": "pending"
  }
  ```

### 2.4 Lihat / Update Tugas

- **Update (full/partial)**
  - **Method**: PUT atau PATCH
  - **URL**: `/api/tasks/{id}`
  - **Body (JSON, contoh)**:
    ```json
    {
      "title": "Tugas PBO revisi",
      "status": "completed"
    }
    ```

### 2.5 Tandai Selesai

- **Method**: PATCH
- **URL**: `/api/tasks/{id}/complete`

### 2.6 Hapus Tugas

- **Method**: DELETE
- **URL**: `/api/tasks/{id}`

---

## 3. Catatan (Notes)

Header:
- `Authorization: Bearer <TOKEN>`

Model: `App\Models\Note`

Field penting:
- `title` (string)
- `content` (string)
- `category` (string, default: `personal`)

### 3.1 List Catatan

- **Method**: GET
- **URL**: `/api/notes`

### 3.2 Detail Catatan

- **Method**: GET
- **URL**: `/api/notes/{id}`

### 3.3 Buat Catatan

- **Method**: POST
- **URL**: `/api/notes`
- **Body (JSON)**:
  ```json
  {
    "title": "Catatan kuliah PBO",
    "content": "Ringkasan materi minggu ini",
    "category": "kuliah"
  }
  ```

### 3.4 Update Catatan

- **Method**: PUT / PATCH
- **URL**: `/api/notes/{id}`
- **Body (JSON)**: field yang ingin diubah.

### 3.5 Hapus Catatan

- **Method**: DELETE
- **URL**: `/api/notes/{id}`

---

## 4. Kalender (Activities)

Header:
- `Authorization: Bearer <TOKEN>`

Model: `App\Models\Activity`

Field penting:
- `title` (string)
- `desk` (deskripsi, string)
- `date` (date, required)
- `time` (HH:MM, disimpan string)
- `status` (pending|selesai)
- `reminder` (string, default: `none`)

### 4.1 List Aktivitas

- **Method**: GET
- **URL**: `/api/activities`

### 4.2 Detail Aktivitas

- **Method**: GET
- **URL**: `/api/activities/{id}`

### 4.3 Buat Aktivitas

- **Method**: POST
- **URL**: `/api/activities`
- **Body (JSON)**:
  ```json
  {
    "title": "Presentasi proyek",
    "desk": "Presentasi di kelas A",
    "date": "2025-12-21",
    "time": "09:00",
    "status": "pending"
  }
  ```

### 4.4 Update Aktivitas

- **Method**: PUT / PATCH
- **URL**: `/api/activities/{id}`
- **Body (JSON)**: field yang ingin diubah.

### 4.5 Hapus Aktivitas

- **Method**: DELETE
- **URL**: `/api/activities/{id}`

---

## 5. Materi (Folders & Files)

Header:
- `Authorization: Bearer <TOKEN>`

Model:
- `App\Models\Folder`
- `App\Models\File`

### 5.1 Folders

#### 5.1.1 List Folder

- **Method**: GET
- **URL**: `/api/folders`
- **Query (opsional)**:
  - `parent_id`: ID folder induk. Jika tidak diisi → folder root.

#### 5.1.2 Detail Folder

- **Method**: GET
- **URL**: `/api/folders/{id}`

#### 5.1.3 Buat Folder

- **Method**: POST
- **URL**: `/api/folders`
- **Body (JSON)**:
  ```json
  {
    "name": "Folder Kuliah",
    "parent_id": null,
    "color": "blue"
  }
  ```

#### 5.1.4 Update Folder

- **Method**: PUT / PATCH
- **URL**: `/api/folders/{id}`
- **Body (JSON)**:
  ```json
  {
    "name": "Folder Kuliah Semester 5",
    "color": "green"
  }
  ```

#### 5.1.5 Hapus Folder

- **Method**: DELETE
- **URL**: `/api/folders/{id}`
- **Catatan**: folder harus kosong (tidak ada sub-folder & file).

---

### 5.2 Files

#### 5.2.1 List File

- **Method**: GET
- **URL**: `/api/files`
- **Query (opsional)**:
  - `folder_id`: filter file di folder tertentu.

#### 5.2.2 Detail File

- **Method**: GET
- **URL**: `/api/files/{id}`

#### 5.2.3 Upload File

- **Method**: POST
- **URL**: `/api/files`
- **Body (form-data)**:
  - `file` (Type: File) → pdf/doc/docx/xls/xlsx/jpg/jpeg/png/gif, max 10MB.
  - `folder_id` (text, opsional) → ID folder tujuan.

#### 5.2.4 Rename File

- **Method**: PUT / PATCH
- **URL**: `/api/files/{id}`
- **Body (JSON)**:
  ```json
  {
    "name": "modul_pbo_1"
  }
  ```

#### 5.2.5 Hapus File

- **Method**: DELETE
- **URL**: `/api/files/{id}`

#### 5.2.6 Download File

- **Method**: GET
- **URL**: `/api/files/{id}/download`

#### 5.2.7 Preview File

- **Method**: GET
- **URL**: `/api/files/{id}/preview`

---

## 6. Ping (Tes API)

- **Method**: GET
- **URL**: `/api/ping`
- **Response**:
  ```json
  {
    "message": "API OK"
  }
  ```

---

## 7. Tips Penggunaan di Postman

1. Jalankan server Laravel:
   ```bash
   php artisan serve
   ```
2. Panggil `POST /api/auth/register` atau `POST /api/auth/login` untuk dapatkan `token`.
3. Di Postman, pada tab **Authorization**:
   - Type: **Bearer Token**
   - Token: isi dengan nilai `token` dari response.
4. Gunakan koleksi request untuk tiap resource (tasks, notes, activities, folders, files).
