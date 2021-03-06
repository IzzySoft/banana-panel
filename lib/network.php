<?php

namespace lib;

class Network {

    public static function connections() {
        global $ssh;
        $connections = $ssh->shell_exec_noauth("netstat -nta --inet | grep -e '\(tcp\|udp\)' | wc -l");
        $established = $ssh->shell_exec_noauth("netstat -nta --inet | grep ESTABLISHED | wc -l");
        $listening = $ssh->shell_exec_noauth("netstat -nta --inet | grep LISTEN | wc -l");
        $opening = $ssh->shell_exec_noauth("netstat -nta --inet | grep SYN | wc -l");
        $closing = $ssh->shell_exec_noauth("netstat -nta --inet | grep -e '\(CLOS\|WAIT\|LAST\)' | wc -l");

        return array(
            'connections' => substr($connections, 0, -1),
            'established' => substr($established, 0, -1),
            'listening' => substr($listening, 0, -1),
            'opening' => substr($opening, 0, -1),
            'closing' => substr($closing, 0, -1),
            'alert' => ($connections >= 50 ? 'warning' : 'success')
        );
    }

    public static function ethernet() {
        global $ssh;
        $data = $ssh->shell_exec_noauth("/sbin/ifconfig eth0 | grep RX\ bytes");
        $data = str_ireplace("RX bytes:", "", $data);
        $data = str_ireplace("TX bytes:", "", $data);
        $data = trim($data);
        $data = explode(" ", $data);

        $rxRaw = $data[0] / 1024 / 1024;
        $txRaw = $data[4] / 1024 / 1024;
        $rx = round($rxRaw, 2);
        $tx = round($txRaw, 2);

        return array(
            'up' => $tx,
            'down' => $rx,
            'total' => $rx + $tx
        );
    }

}
