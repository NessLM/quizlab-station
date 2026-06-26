// =====================================================================
//  QuizResultSender.cs — Pengirim hasil quiz dari Unity (VR) ke web
//  Proyek: QuizLab Station — SMKN 5 Pangkalpinang
// ---------------------------------------------------------------------
//  CARA MEMASANG DI UNITY:
//   1. Buat folder "Scripts" di dalam Assets (kalau belum ada),
//      lalu salin file ini ke sana.
//   2. Di Hierarchy, buat GameObject kosong, beri nama "QuizResultSender".
//   3. Drag file ini ke GameObject tersebut (Add Component).
//   4. Di Inspector, sesuaikan "Server Url" dan "Kunci Rahasia" bila perlu.
//   5. Saat quiz di VR selesai, panggil method publik:
//
//          quizResultSender.KirimHasil(idSiswa, jumlahBenar, jumlahSalah);
//
//      Contoh dari script lain:
//          public QuizResultSender pengirim;   // drag dari Inspector
//          void QuizSelesai() {
//              pengirim.KirimHasil(5, 8, 2);    // id=5, benar=8, salah=2
//          }
// ---------------------------------------------------------------------
//  CATATAN PENTING (headset Quest / Android):
//   - "localhost" di dalam headset menunjuk ke headset itu SENDIRI,
//     bukan ke PC server. Jadi saat build APK & dijalankan di Quest,
//     GANTI "localhost" pada Server Url dengan IP PC server di jaringan
//     yang sama, contoh:  http://192.168.1.10/quizlab-station/update.php
//   - Cek IP PC: buka CMD di Windows lalu ketik  ipconfig
//     (lihat "IPv4 Address"). PC & Quest harus 1 WiFi/router yang sama.
//   - Pastikan firewall mengizinkan Apache (port 80).
// =====================================================================

using System.Collections;
using UnityEngine;
using UnityEngine.Networking;

public class QuizResultSender : MonoBehaviour
{
    [Header("Pengaturan Server")]
    [Tooltip("Alamat update.php. Ganti localhost dengan IP PC saat dijalankan di Quest.")]
    public string serverUrl = "http://localhost/quizlab-station/update.php";

    [Tooltip("Harus SAMA persis dengan KUNCI_RAHASIA di update.php")]
    public string kunciRahasia = "quizlab-rahasia-2026";

    /// <summary>
    /// Panggil method ini saat quiz selesai untuk mengirim nilai ke web.
    /// </summary>
    /// <param name="idSiswa">ID siswa dari pendaftaran web</param>
    /// <param name="benar">Jumlah jawaban benar</param>
    /// <param name="salah">Jumlah jawaban salah</param>
    public void KirimHasil(int idSiswa, int benar, int salah)
    {
        // Jalankan sebagai coroutine agar tidak membekukan game saat menunggu jaringan
        StartCoroutine(KirimHasilCoroutine(idSiswa, benar, salah));
    }

    private IEnumerator KirimHasilCoroutine(int idSiswa, int benar, int salah)
    {
        // Susun data form yang akan dikirim via POST
        WWWForm form = new WWWForm();
        form.AddField("id", idSiswa);
        form.AddField("benar", benar);
        form.AddField("salah", salah);
        form.AddField("kunci", kunciRahasia);

        using (UnityWebRequest www = UnityWebRequest.Post(serverUrl, form))
        {
            // Kirim permintaan dan tunggu balasan server
            yield return www.SendWebRequest();

            if (www.result == UnityWebRequest.Result.Success)
            {
                // Server membalas JSON: { "sukses": ..., "pesan": "..." }
                Debug.Log("[QuizLab] Berhasil. Balasan server: " + www.downloadHandler.text);
            }
            else
            {
                // Gagal: cek koneksi, IP server, atau firewall
                Debug.LogError("[QuizLab] Gagal mengirim hasil: " + www.error
                               + " | URL: " + serverUrl);
            }
        }
    }
}
