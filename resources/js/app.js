import Swal from 'sweetalert2';

// Buat instance SweetAlert2 dengan tema custom yang sesuai desain Fundlink
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    },
});

// Expose ke window agar bisa dipakai dari mana saja
window.Swal   = Swal;
window.Toast  = Toast;

// ─── Livewire Event Listeners ────────────────────────────────────────────────

document.addEventListener('livewire:initialized', () => {

    // Flash sukses → toast hijau
    Livewire.on('swal-success', (data) => {
        Toast.fire({
            icon: 'success',
            title: data[0]?.message ?? data[0] ?? 'Berhasil!',
        });
    });

    // Flash error → toast merah
    Livewire.on('swal-error', (data) => {
        Toast.fire({
            icon: 'error',
            title: data[0]?.message ?? data[0] ?? 'Terjadi kesalahan.',
        });
    });

    // Flash info → toast biru
    Livewire.on('swal-info', (data) => {
        Toast.fire({
            icon: 'info',
            title: data[0]?.message ?? data[0] ?? 'Informasi.',
        });
    });

    // Konfirmasi hapus → dialog modal, callback ke Livewire
    // Penggunaan: $this->dispatch('swal-confirm', id: $id, action: 'deleteTransaction')
    Livewire.on('swal-confirm', (data) => {
        const payload = data[0];
        Swal.fire({
            title: payload.title ?? 'Yakin ingin menghapus?',
            text: payload.text ?? 'Data yang dihapus tidak bisa dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#e5e7eb',
            confirmButtonText: payload.confirmText ?? 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                cancelButton: '!text-gray-700',
                popup: '!rounded-3xl !shadow-2xl',
                title: '!font-black !text-gray-900 !text-xl',
                htmlContainer: '!text-gray-400 !text-sm',
                confirmButton: '!rounded-xl !font-black !text-xs !uppercase !tracking-widest !px-6 !py-3',
                cancelButton: '!rounded-xl !font-black !text-xs !uppercase !tracking-widest !px-6 !py-3',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                // Panggil method Livewire yang dikirim bersama event
                const component = Livewire.find(payload.componentId);
                if (component && payload.action) {
                    component.call(payload.action, payload.id);
                }
            }
        });
    });

});
