<?php

class ApiGenerator {
    public function generate($api, $name) {
        $typescript_output = "/** ### This file is auto-generated, modifying is futile! ### */\n\n";
        $typescript_exported_types = '';
        $typescript_endpoint_enum = '';
        $typescript_request_types = '';
        $typescript_response_types = '';

        $typescript_endpoint_enum .= "// eslint-disable-next-line no-shadow\n";
        $typescript_endpoint_enum .= "export enum {$name}Endpoint {\n";
        $typescript_request_types .= "export interface {$name}Requests extends {$name}EndpointMapping {\n";
        $typescript_response_types .= "export interface {$name}Responses extends {$name}EndpointMapping {\n";
        foreach ($api->endpoints as $endpoint_name => $endpoint_definition) {
            $endpoint = $endpoint_definition();
            $typescript_endpoint_enum .= "    {$endpoint_name} = '{$endpoint_name}',\n";
            $typescript_request_types .= "    {$endpoint_name}: {\n";
            foreach ($endpoint->getRequestFields() as $field_id => $field) {
                foreach ($field->getExportedTypeScriptTypes() as $type_ident => $exported_type) {
                    $typescript_exported_types .= "export type {$type_ident} = {$exported_type};\n";
                }
                $type = $field->getTypeScriptType(['should_substitute' => true]);
                $indented_type = str_replace("\n", "\n        ", $type);
                $typescript_request_types .= "        {$field_id}: {$indented_type},\n";
            }
            $typescript_request_types .= "    },\n";
            $typescript_response_types .= "    {$endpoint_name}: {\n";
            foreach ($endpoint->getResponseFields() as $field_id => $field) {
                foreach ($field->getExportedTypeScriptTypes() as $type_ident => $exported_type) {
                    $typescript_exported_types .= "export type {$type_ident} = {$exported_type};\n";
                }
                $type = $field->getTypeScriptType(['should_substitute' => true]);
                $indented_type = str_replace("\n", "\n        ", $type);
                $typescript_response_types .= "        {$field_id}: {$indented_type},\n";
            }
            $typescript_response_types .= "    },\n";
        }
        $typescript_endpoint_enum .= "}\n";
        $typescript_request_types .= "}\n";
        $typescript_response_types .= "}\n";

        $typescript_output .= "{$typescript_exported_types}\n";
        $typescript_output .= "{$typescript_endpoint_enum}\n";
        $typescript_output .= "type {$name}EndpointMapping = {[key in {$name}Endpoint]: {[fieldId: string]: any}};\n\n";
        $typescript_output .= "{$typescript_request_types}\n";
        $typescript_output .= "{$typescript_response_types}\n";
        return $typescript_output;
    }
}
