<?php

namespace App\Events;

use App\Models\FuncionAsiento;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AsientoBloqueado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $asiento;

    /**
     * Create a new event instance.
     */
    public function __construct(FuncionAsiento $asiento)
    {
        $this->asiento = $asiento;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('cinema.' . $this->asiento->funcion_id),
        ];
    }

    /**
     * Data to broadcast
     */
    public function broadcastWith(): array
    {
        return [
            'codigo_asiento' => $this->asiento->codigo_asiento,
            'estado' => $this->asiento->estado,
            'reservado_hasta' => $this->asiento->reservado_hasta ? $this->asiento->reservado_hasta->format('Y-m-d H:i:s') : null,
        ];
    }
}
