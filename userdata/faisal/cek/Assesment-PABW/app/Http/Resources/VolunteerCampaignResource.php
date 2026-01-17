<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerCampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'judul' => $this->judul,
            'slug' => $this->slug,
            'deskripsi' => $this->deskripsi,
            'lokasi' => $this->lokasi,
            'tanggal_mulai' => $this->tanggal_mulai ? (is_string($this->tanggal_mulai) ? $this->tanggal_mulai : $this->tanggal_mulai->format('Y-m-d')) : null,
            'tanggal_selesai' => $this->tanggal_selesai ? (is_string($this->tanggal_selesai) ? $this->tanggal_selesai : $this->tanggal_selesai->format('Y-m-d')) : null,
            'formatted_tanggal_mulai' => $this->tanggal_mulai ? (is_string($this->tanggal_mulai) ? date('d M Y', strtotime($this->tanggal_mulai)) : $this->tanggal_mulai->format('d M Y')) : null,
            'formatted_tanggal_selesai' => $this->tanggal_selesai ? (is_string($this->tanggal_selesai) ? date('d M Y', strtotime($this->tanggal_selesai)) : $this->tanggal_selesai->format('d M Y')) : null,
            'kategori' => $this->kategori,
            'kuota_total' => $this->kuota_total,
            'kuota_terisi' => $this->kuota_terisi,
            'progress' => $this->progress, // Using the accessor
            'status' => $this->status,
            'image' => $this->image, // This will use the accessor from the model
            'created_at' => $this->created_at ? (is_string($this->created_at) ? date('Y-m-d H:i:s', strtotime($this->created_at)) : $this->created_at->format('Y-m-d H:i:s')) : null,
            'updated_at' => $this->updated_at ? (is_string($this->updated_at) ? date('Y-m-d H:i:s', strtotime($this->updated_at)) : $this->updated_at->format('Y-m-d H:i:s')) : null,
        ];
    }
}
