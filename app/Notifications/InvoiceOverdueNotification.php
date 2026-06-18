<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class InvoiceOverdueNotification
 * 
 * Notifies users/finance when an invoice is overdue.
 * 
 * @package App\Notifications
 */
class InvoiceOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Invoice
     */
    protected Invoice $invoice;

    /**
     * Create a new notification instance.
     * 
     * @param Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     * 
     * @param object $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject('Peringatan: Invoice Jatuh Tempo - ' . $this->invoice->number)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Invoice berikut telah melewati tanggal jatuh tempo pembayaran:')
            ->line('**Nomor Invoice:** ' . $this->invoice->number)
            ->line('**Klien:** ' . ($this->invoice->client ? $this->invoice->client->company : 'N/A') . ' (' . ($this->invoice->client ? $this->invoice->client->name : 'N/A') . ')')
            ->line('**Tanggal Terbit:** ' . $this->invoice->issue_date->format('d M Y'))
            ->line('**Jatuh Tempo:** ' . $this->invoice->due_date->format('d M Y'))
            ->line('**Total Tagihan:** Rp ' . number_format($this->invoice->total, 0, ',', '.'))
            ->action('Lihat Detail Invoice', url('/invoices/' . $this->invoice->id))
            ->line('Harap segera melakukan follow-up pembayaran ke pihak klien.');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->number,
            'client_name' => $this->invoice->client ? $this->invoice->client->name : 'N/A',
            'client_company' => $this->invoice->client ? $this->invoice->client->company : 'N/A',
            'total' => $this->invoice->total,
            'due_date' => $this->invoice->due_date->toDateString(),
            'event' => 'overdue',
            'title' => 'Invoice Jatuh Tempo',
            'message' => "Invoice {$this->invoice->number} untuk " . ($this->invoice->client ? $this->invoice->client->company : 'N/A') . " telah melewati tanggal jatuh tempo.",
        ];
    }
}
